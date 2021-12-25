<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $isLoginSuccess =  Auth::attempt([
            "email" => $request->email,
            "password" => $request->password
        ], 1);

        // return $isLoginSuccess ? 
    }
}
