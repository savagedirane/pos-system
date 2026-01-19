<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // placeholder: authenticate user
        return redirect()->route('pos.index');
    }

    public function logout(Request $request)
    {
        // placeholder: logout
        return redirect()->route('login');
    }
}
