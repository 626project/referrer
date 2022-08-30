<?php

namespace App\Http\Controllers;

use App\Models\ReferrerLink;
use App\Models\TgUser;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    /**
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return Renderable
     */
    public function dashboard()
    {
        $referrer_links = ReferrerLink::get();

        return view('dashboard', [
            'referrer_links' => $referrer_links,
        ]);
    }

    /**
     * @return Renderable
     */
    public function create_link_page()
    {
        return view('create_link');
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector|void
     */
    public function create_link(Request $request)
    {
        $label = $request->get('label', '');
        $link_label = $label ? $label : rand(0, 100) . time() . rand(0, 100);
        ReferrerLink::create([
            'link' => config('app.url') . '/invite/' . $link_label,
            'label' => $label,
            'caption' => $request->get('caption', '') ?? '',
            'count' => 0,
            'uniq_count' => 0,
        ]);

        return redirect('dashboard');
    }

    /**
     * @param Request $request
     * @param int $link_id
     * @return Application|RedirectResponse|Redirector|void
     */
    public function delete_link(
        Request $request,
        int $link_id
    )
    {
        $link = ReferrerLink::find($link_id);
        if ($link) {
            $link->delete();
        }

        return redirect('dashboard');
    }

    /**
     * @param Request $request
     * @param int $link_id
     * @return Renderable
     */
    public function show_tg_users(Request $request, int $link_id)
    {
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $tg_users = $this->get_tg_users($request, $link_id);

        return view('users', [
            'tg_users' => $tg_users,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'link_id' => $link_id,
        ]);
    }

    /**
     * @param Request $request
     * @param int $link_id
     * @return void
     */
    public function download(Request $request, int $link_id)
    {
        info(1);
        $tg_users = $this->get_tg_users($request, $link_id);
        Excel::create('result_' . date('Y-m-d'), function($excel) use ($tg_users) {
            $excel->setTitle('Result download');
            $excel->setCreator('Me')->setCompany('alternativeassistance.ru');
            $excel->setDescription('Статистика действий пользователей');

            $titles = ['id', 'tg id', 'профиль', 'имя', 'фамилия', 'действие', 'дата'];
            $excel->sheet('Sheet 1', function ($sheet) use ($titles, $tg_users) {
                $sheet->setOrientation('landscape');
                $sheet->fromArray($titles);
                foreach ($tg_users as $tg_user) {
                    $row = [
                        $tg_user['id'],
                        $tg_user['tg_id'],
                        $tg_user['username'],
                        $tg_user['first_name'],
                        $tg_user['last_name'],
                        $tg_user['last_action'],
                        $tg_user['created_at'],
                    ];
                    $sheet->fromArray($row);
                }
            });
        })->download('xls');
        info(2);
    }

    /**
     * @param Request $request
     * @param int $link_id
     * @return mixed
     */
    private function get_tg_users(
        Request $request,
        int $link_id
    ) {
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $tg_users_request = TgUser::where(['link_id' => $link_id])
            ->whereIn('last_action', ['/start', 'variant 1', 'variant 2', 'variant 3', 'call manager', 'send a scan of your passport', 'need info about banks', 'i am ready', 'want a card', 'want a card. not resident', 'want a card. have inn', 'your question', 'participation in the action']);
        if ($start_date) {
            $tg_users_request->where('created_at', '>=', $start_date);
        }
        if ($end_date) {
            $tg_users_request->where('created_at', '<', $end_date);
        }

        return $tg_users_request->get();
    }
}
