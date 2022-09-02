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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class ProfileController extends Controller
{
    /**
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * @return Renderable
     */
    public function show()
    {
        return view('profile', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function edit(Request $request)
    {
        $actual_password = $request->get('actual_password');
        $new_password = $request->get('new_password');
        $new_password_confirm = $request->get('new_password_confirm');
        $user = Auth::user();
        info('request: ' . print_r($request->all(), true));
        if (Hash::check($actual_password, $user->password) && $new_password === $new_password_confirm) {
            $user->password = Hash::make($new_password);
            $user->save();

            return 'success. <a href="/profile">back</a>';
        }

        return 'error. <a href="/profile">back</a>';
    }
}
