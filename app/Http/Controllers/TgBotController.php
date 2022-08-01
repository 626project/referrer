<?php

namespace App\Http\Controllers;

use App\Models\ReferrerLink;
use App\Models\ReferrerRedirect;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class TgBotController extends Controller
{
    const TOKEN = '5402169870:AAGd1B4gqLVz_1F6pLWVjh5fBVXuOadRqgw';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * @return void
     */
    public function index()
    {
        $data = json_decode(file_get_contents('php://input'), TRUE);
        // log input data
        info('input data: ' . print_r($data, 1));

        $data = isset($data['callback_query']) ? $data['callback_query'] : $data['message'];

        define('TOKEN', self::TOKEN);

        $message = mb_strtolower(($data['text'] ? $data['text'] : $data['data']), 'utf-8');

                $method = 'sendMessage';
                $link = ReferrerLink::find(8);
                $send_data = [
                    'text' => $link->caption,
                    'keyboard' => [
                        [
                            ['text' => 'test 22'],
                        ],
                    ],
                ];

        $send_data['chat_id'] = $data['chat'] ['id'];
        $res = self::sendTelegram($method, $send_data);
    }

    /**
     * @param $method
     * @param $data
     * @param array $headers
     * @return bool|mixed|string
     */
    private function sendTelegram($method, $data, $headers = [])
    {
        $curl = curl_init();
        info('$method: ' . print_r($method, true));//fixme
        info('data: ' . print_r($data, true));//fixme
        curl_setopt_array($curl, [
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://api.telegram.org/bot' . self::TOKEN . '/' . $method,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array_merge(array("Content-Type: application/json"))
        ]);
        $result = curl_exec($curl);
        curl_close($curl);

        return (json_decode($result, 1) ? json_decode($result, 1) : $result);
    }

    /**
     * Show the application dashboard.
     *
     * @param string $code
     * @return Application|RedirectResponse|Redirector|void
     */
    public function invite(string $code)
    {
        $referrer_link = ReferrerLink::where([
            'link' => config('app.url') . '/invite/' . $code,
        ])->first();
        if (!$referrer_link) {
            return redirect('login');
        }
        $referrer_link->increment('count');
        $ip = $_SERVER['REMOTE_ADDR'];
        $referrer_uniq_redirect_count = ReferrerRedirect::where([
            'ip' => $ip,
            'referrer_link_id' => $referrer_link->id,
        ])->count();
        if ($referrer_uniq_redirect_count === 0) {
            $referrer_link->increment('uniq_count');
        }
        ReferrerRedirect::create([
            'ip' => $ip,
            'referrer_link_id' => $referrer_link->id,
        ]);

        return redirect('http://t.me/Info24PlatformBot');
//        return redirect('http://t.me/AlternativeAssistance');
    }
}
