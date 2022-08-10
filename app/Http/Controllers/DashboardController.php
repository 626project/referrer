<?php

namespace App\Http\Controllers;

use App\Models\ReferrerLink;
use App\Models\ReferrerRedirect;
use App\Models\TgUser;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

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
     * @param int $link_id
     * @return Renderable
     */
    public function show_tg_users(int $link_id)
    {
        $tg_users = TgUser::where(['link_id' => $link_id])->get();

        return view('users', [
            'tg_users' => $tg_users,
        ]);
    }
}
