<?php

namespace App\Http\Controllers;

use App\Models\ReferrerLink;
use App\Models\TgUser;
use App\Models\TgUserExportCollection;
use App\Services\UserService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    private $excel;

    private $user_service;

    /**
     * @param Excel $excel
     * @param UserService $user_service
     */
    public function __construct(
        Excel $excel,
        UserService $user_service
    ) {
        $this->middleware('auth');

        $this->excel = $excel;
        $this->user_service = $user_service;
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
        $label_elements = ReferrerLink::where('label', $link_label)->first();
        if ($label_elements) {
            return redirect()->back();
        }
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
        $tg_users = $this->user_service->get_tg_users($link_id, $start_date, $end_date);

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
        $export_collection = new TgUserExportCollection($link_id, $request->get('start_date'), $request->get('end_date'));

        return ($export_collection)->download('result_' . date('Y-m-d') . '.xlsx');
    }
}
