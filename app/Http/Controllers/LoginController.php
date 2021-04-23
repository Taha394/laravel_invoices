<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function getLogin()
    {
       return view('auth.login');
    }

    public function login(RequestLogin $request)
    {
        $remember_me = $request->has('remember_me');

        if (auth()->attempt(['email' => $request->input("email"), 'password' => $request->input("password")], $remember_me)) {
            // notify()->success('تم الدخول بنجاح  ');
            return redirect()-> route('home');
        }
        return redirect()->back()->with(['error' => 'هناك خطاء في البيانات']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('get.login');
    }
}
