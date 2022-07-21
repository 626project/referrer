<?php

namespace App\Services;

use App\Models\BlackListPhone;
use App\Models\BlockedSmsRequest;
use App\Models\ClientFirebaseToken;
use App\Models\GuestRequests;
use App\Models\Link;
use App\Models\Sms;
use App\Models\User;
use App\Models\UserRequest;
use App\Models\Vacancy;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use ReCaptcha\ReCaptcha;

class UserService extends UserRepository
{
    /**
     * @var SmsService
     */
    private $sms_service;

    /**
     * @var SettingService
     */
    private $setting_service;

    /**
     * @param SmsService $sms_service
     * @param SettingService $setting_service
     */
    public function __construct(
        SmsService $sms_service,
        SettingService $setting_service
    )
    {
        $this->sms_service = $sms_service;
        $this->setting_service = $setting_service;
    }

    /**
     * @param string|null $ip
     * @return bool
     */
    public function guest_registration_is_available(?string $ip)
    {
        if ($ip) {
            // find quest requests
            $guest_request = GuestRequests::firstOrCreate(['ip' => $ip]);
            return $guest_request->count < config('app.count_max_guest_registrations_by_one_ip');
        }

        return false;
    }

    /**
     * @param string|null $ip
     */
    public function inc_guest_registration(?string $ip)
    {
        if ($ip) {
            GuestRequests::firstOrCreate(['ip' => $ip])->increment('count');
        }
    }

    /**
     * @param int|null $language_id
     * @param int|null $region_id
     * @param int|null $client_timestamp
     * @param int|null $link_id
     * @param int|null $referrer_id
     * @param string|null $type
     * @return User|bool
     */
    public function guest_registration(
        ?int $language_id,
        ?int $region_id,
        ?int $client_timestamp,
        ?int $link_id,
        ?int $referrer_id = null,
        ?string $type = User::TYPE_EMPLOYER
    )
    {
        if (!$language_id || is_null($region_id)) {
            return false;
        }

        return $this->create_user(
            $language_id,
            $region_id,
            null,
            null,
            $client_timestamp,
            $link_id,
            $referrer_id,
            $type
        );
    }

    /**
     * @param int $client_timestamp
     * @return float|int
     */
    private function get_timezone_minutes(int $client_timestamp)
    {
        $minutes = intdiv(($client_timestamp - time()), 60);
        $difference_seconds = $minutes - (intdiv($minutes, 15) * 15);
        if ($difference_seconds !== 0) {
            $minutes += $difference_seconds ? (15 - $difference_seconds) : -(15 + $difference_seconds);
        }
        $minutes = $minutes > 1440 || $minutes < -1440 ? 0 : $minutes;

        return $minutes;
    }

    /**
     * @param User $user
     * @param string $firebase_token
     * @param string $jwt_token
     * @return bool
     */
    public function create_token(User $user, string $firebase_token, string $jwt_token)
    {
        $md5_jwt_token = md5($jwt_token);
        // find same token
        $user_token = ClientFirebaseToken::where([
            'firebase_token' => $firebase_token,
        ])->first();
        if ($user_token) {
            if ($user_token->jwt_token !== $md5_jwt_token) {
                $user_token->update([
                    'jwt_token' => $md5_jwt_token,
                ]);
            }
        } else {
            $user_token = ClientFirebaseToken::firstOrCreate([
                'client_id' => $user->id,
                'firebase_token' => $firebase_token,
                'jwt_token' => $md5_jwt_token,
            ]);
        }

        return $user_token;
    }

    /**
     * @param $phone
     * @param $phone_code
     * @param $client_timestamp
     * @return bool|object
     */
    public function login($phone, $phone_code, $client_timestamp)
    {
        $user = $this->find_user_by_phone($phone);
        if (!$user) {
            return false;
        }
        if (!self::check_sms_code($phone_code, $phone)) {
            return false;
        }
        $minutes = $this->get_timezone_minutes($client_timestamp);
        $user->update([
            'timezone_minutes' => $minutes ?? 0,
            'one_time_code' => null,
        ]);

        return $user;
    }

    /**
     * @param $language_id
     * @param $region_id
     * @param $name
     * @param $phone
     * @param $phone_code
     * @param $client_timestamp
     * @param $link_id
     * @param $referrer_id
     * @param $type
     * @return bool
     */
    public function registration(
        $language_id,
        $region_id,
        $name,
        $phone,
        $phone_code,
        $client_timestamp,
        $link_id,
        $referrer_id,
        ?string $type = User::TYPE_EMPLOYER
    )
    {
        $settings = $this->setting_service->get();
        if (!$language_id || is_null($region_id)) {
            return false;
        }
        if (!self::check_sms_code($phone_code, $phone)) {
            if (!($settings->reg_without_sms && $phone_code === null)) {
                return false;
            }
        }

        return $this->create_user($language_id, $region_id, $name, $phone, $client_timestamp, $link_id, $referrer_id, $type);
    }

    /**
     * @param $language_id
     * @param $region_id
     * @param $name
     * @param $phone
     * @param $client_timestamp
     * @param $link_id
     * @param int|null $referrer_id
     * @param string|null $type
     * @return mixed
     */
    public function create_user(
        $language_id,
        $region_id,
        $name,
        $phone,
        $client_timestamp,
        $link_id,
        ?int $referrer_id = null,
        ?string $type = User::TYPE_EMPLOYER
    )
    {
        // find user
        if ($phone) {
            $user = $this->find_user_by_phone($phone);
            if ($user) {
                return false;
            }
        }

        $salt = substr(md5(microtime(true)), 0, 5);
        $password = substr(sha1($salt), 0, 12);
        $minutes = $this->get_timezone_minutes($client_timestamp);
        $label = '';
        if ($link_id) {
            $link = Link::find($link_id);
            $label = $link ? $link->label : '';
        }
        $is_guest = $phone ? false : true;

        return User::create([
            'language_id' => $language_id,
            'region_id' => $region_id,
            'name' => $name,
            'role' => User::ROLE_CLIENT,
            'email' => $salt . '_' . time() . '_baraka@baraka.app',
            'phone' => $phone,
            'one_time_code' => null,
            'is_guest' => $is_guest,
            'reg_ip' => $_SERVER['REMOTE_ADDR'],
            'timezone_minutes' => $minutes ?? 0,
            'password' => bcrypt($password),
            'link_id' => $link_id,
            'label' => $label,
            'referrer_id' => $referrer_id,
            'type' => $type,
            'paid_status' => User::PAID_STATUS_NO,
        ]);
    }

    /**
     * @param $phone
     * @param string $hash
     * @param string $recaptcha_value
     * @return bool
     */
    public function send_sms($phone, string $hash, ?string $recaptcha_value = '')
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        // check country code
        if (
            strpos($phone, '+7') !== 0 && // россия
            strpos($phone, '+996') !== 0 && // киргизия
            strpos($phone, '+992') !== 0 && // таджикистан
            strpos($phone, '+998') !== 0 // узбекистан
        ) {
            // log blocked sms request
            BlockedSmsRequest::create([
                'phone' => $phone,
                'ip' => $ip,
                'type' => BlockedSmsRequest::TYPE_WRONG_COUNTRY_CODE,
            ]);

            return false;
        }
        if ($recaptcha_value) {
            // check sms recapture
            $secret = env('RECAPTCHA_SECRET_KEY');
            require_once(dirname(__FILE__) . '/../Entity/ReCaptcha/autoload.php');
            $recaptcha = new ReCaptcha($secret);
            $resp = $recaptcha->verify($recaptcha_value, $_SERVER['REMOTE_ADDR']);
            if (!$resp->isSuccess()) {
                BlockedSmsRequest::create([
                    'phone' => $phone,
                    'ip' => $ip,
                    'type' => BlockedSmsRequest::TYPE_WRONG_RECAPTCHA,
                ]);

                return false;
            }
        } else {
            // check sms hash
            $salt = Sms::SALT;
            $server_hash = [
                hash('sha256', "{$phone}_{$salt}_" . Carbon::now()->subDay()->format('Ymd')) => 1,
                hash('sha256', "{$phone}_{$salt}_" . Carbon::now()->format('Ymd')) => 1,
                hash('sha256', "{$phone}_{$salt}_" . Carbon::now()->addDay()->format('Ymd')) => 1,
            ];
            if (!isset($server_hash[$hash])) {
                // log blocked sms request
                BlockedSmsRequest::create([
                    'phone' => $phone,
                    'ip' => $ip,
                    'type' => BlockedSmsRequest::TYPE_WRONG_HASH,
                ]);

                return false;
            }
            // check user requests
            $user_requests_count = UserRequest::where('created_at', '>=', Carbon::now()->subMinutes(5))
                ->where([
                    'ip' => $ip,
                ])
                ->where(function ($query) {
                    $query->where(['request' => UserRequest::REQUEST_PROFILE])
                        ->orWhere(['request' => UserRequest::REQUEST_REG_LANGUAGES]);
                })
                ->count();
            if (!$user_requests_count) {
                // log blocked sms request
                BlockedSmsRequest::create([
                    'phone' => $phone,
                    'ip' => $ip,
                    'type' => BlockedSmsRequest::TYPE_NOT_FOUND_USER_REQUESTS,
                ]);

                return false;
            }
        }
        // check send sms limit by phone
        $max_count_sms = strpos($phone, '+7') === 0 ? 2 : 1;
        $sms_from_30_min = Sms::where('created_at', '>=', Carbon::now()->subMinutes(30))
            ->where([
                'phone' => $phone,
            ])
            ->count();
        if ($sms_from_30_min >= $max_count_sms) {
            // log blocked sms request
            BlockedSmsRequest::create([
                'phone' => $phone,
                'ip' => $ip,
                'type' => BlockedSmsRequest::TYPE_MANY_REQUESTS_ON_PHONE,
            ]);

            return false;
        }
        // check send sms access by ip
        $today_sms = Sms::where('created_at', '>=', Carbon::now()->subDay())
            ->where([
                'ip' => $ip,
            ])
            ->count();
        $last_hour_sms = Sms::where('created_at', '>=', Carbon::now()->subHour())
            ->where([
                'ip' => $ip,
            ])
            ->count();
        if ($today_sms >= 10 || $last_hour_sms >= 5) {
            // log blocked sms request
            BlockedSmsRequest::create([
                'phone' => $phone,
                'ip' => $ip,
                'type' => BlockedSmsRequest::TYPE_MANY_REQUESTS,
            ]);

            return false;
        }
        // create sms
        $verification_code = rand(1111, 999999);
        $sms = Sms::create([
            'phone' => $phone,
            'sms_code' => $verification_code,
            'ip' => $ip,
        ]);
        if (!$sms) {
            return false;
        }
        // send sms
        $this->sms_service->notify($phone, 'Baraka: ' . $verification_code);

        return true;
    }

    /**
     * @param $phone
     * @return mixed
     */
    public function find_user_by_phone($phone)
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        $phone = str_replace(' ', '', $phone);
        $user_request = User::where([
            'phone' => $phone,
        ]);
        $first_symbol = substr($phone, 0, 1);
        $replace_length = 1;
        switch ($first_symbol) {
            case '+':
                $replace_length = 2;
                $user_request->orWhere([
                    'phone' => substr_replace($phone, '', 0, 1),
                ]);
            case '7':
            case '8':
                $user_request->orWhere([
                    'phone' => substr_replace($phone, '7', 0, $replace_length),
                ])->orWhere([
                    'phone' => substr_replace($phone, '+7', 0, $replace_length),
                ])->orWhere([
                    'phone' => substr_replace($phone, '8', 0, $replace_length),
                ])->orWhere([
                    'phone' => substr_replace($phone, '+8', 0, $replace_length),
                ]);
                break;
        }

        return $user_request->first();
    }

    /**
     * @param string|null $phone
     * @return string|null
     */
    public function get_prepared_phone(?string $phone)
    {
        if (!$phone) {
            return false;
        }
        $new_phone = preg_replace('/[^0-9+]/', '', $phone);
        $new_phone = str_replace(' ', '', $new_phone);
        switch ($phone[0]) {
            case '0':
            case '1':
            case '2':
            case '3':
            case '4':
            case '5':
            case '6':
            case '7':
            case '9':
                $new_phone = '+' . $phone;

                break;
            case '8':
                $new_phone = substr_replace($phone, '+7', 0, 1);

                break;
            case '+':
                if ($phone[1] === '8') {
                    $new_phone = substr_replace($phone, '+7', 0, 2);
                }

                break;
        }

        return $new_phone;
    }

    /**
     * @param $user
     * @param string $link
     */
    public function update_referral_link($user, string $link)
    {
        $user->update([
            'referral_link' => $link,
        ]);
    }

    /**
     * @param User $user
     * @return int
     */
    public static function get_user_vacancies(User $user)
    {
        $vacancies_count = Vacancy::select('creator_id', DB::raw('count(*) as total'))
            ->where(['creator_id' => $user->id])
            ->groupBy('creator_id')
            ->first();

        return $vacancies_count->total ?? 0;
    }

    /**
     * @param string|null $phone_code
     * @param string $phone
     * @return bool
     */
    public static function check_sms_code(?string $phone_code, string $phone)
    {
        if ($phone_code != Sms::SMS_CODE) {
            $sms = Sms::where([
                'phone' => $phone,
                'sms_code' => $phone_code,
            ])->first();
            if (!$sms) {
                return false;
            }
            $sms->delete();
        }

        return true;
    }

    /**
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public function get_vacancies_by_pages($start_date, $end_date)
    {
        $managers = [];
        $managers_list = User::where(['role' => User::ROLE_ADMIN])
            ->orWhere(['role' => User::ROLE_MANAGER])
            ->get();
        $manager_count = 0;
        foreach ($managers_list as $manager) {
            $vacancies_count = Vacancy::select('status', 'created_at', 'id', 'owner_id')
                ->where('created_at', '>=', $start_date)
                ->where('created_at', '<', $end_date)
                ->where(['creator_id' => $manager->id])
                ->count();
            $managers[] = [
                'id' => $manager->id,
                'email' => $manager->email,
                'vacancies_count' => $vacancies_count,
            ];
            $manager_count++;
        }

        return [
            'list' => $managers,
            'count' => $manager_count,
        ];
    }

    /**
     * @param $start_date
     * @param $end_date
     * @return int
     */
    public function get_vacancies_count($start_date, $end_date)
    {
        $managers_list = User::where(['role' => User::ROLE_ADMIN])
            ->orWhere(['role' => User::ROLE_MANAGER])
            ->get();
        $count = 0;
        foreach ($managers_list as $manager) {
            $vacancies_count = Vacancy::select('status', 'created_at', 'id', 'owner_id')
                ->where('created_at', '>=', $start_date)
                ->where('created_at', '<', $end_date)
                ->where(['creator_id' => $manager->id])
                ->count();
            $count += $vacancies_count;
        }

        return $count;
    }

    /**
     * @param $phone
     * @return mixed
     */
    public function find_in_black_list($phone)
    {
        return BlackListPhone::where(['phone' => $phone])->count();
    }
}
