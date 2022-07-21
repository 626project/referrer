<?php

namespace App\Http\Controllers;

use App\Models\ReferrerLink;
use App\Models\ReferrerRedirect;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
        $referrer_links = ReferrerLink::get();

        return view('dashboard', [
            'referrer_links' => $referrer_links,
        ]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create_link_page()
    {
        return view('create_link');
    }

    /**
     * Show the application dashboard.
     *
     * @param Request $request
     * @return Application|RedirectResponse|Redirector|void
     */
    public function create_link(Request $request)
    {
        ReferrerLink::create([
            'link' => config('app.url') . '/invite/' . rand(0, 100) . time() . rand(0, 100),
            'caption' => $request->get('caption'),
            'count' => 0,
            'uniq_count' => 0,
        ]);

        return redirect('dashboard');
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
    }
}
