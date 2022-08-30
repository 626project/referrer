<?php

namespace App\Http\Controllers;

use App\Models\ReferrerLink;
use App\Models\TgUser;
use App\Models\TgUserExportCollection;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    private $excel;

    /**
     * @return void
     */
    public function __construct(Excel $excel)
    {
        $this->middleware('auth');

        $this->excel = $excel;
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
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(Request $request, int $link_id)
    {
        return (new TgUserExportCollection($link_id, $request->get('start_date'), $request->get('end_date')))->download('invoices.xlsx');
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
