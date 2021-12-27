<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Cookie;


class AuthController extends Controller
{
    public function login(Request $request, AuthService $auth)
    {
        $this->validate($request, [
            "email" => "required|email",
            "password" => "required"
        ]);

        $userAndToken = $auth->attemptAndCreateTokenIfSuccess(
            $request->email,
            $request->password
        );

        if (!$userAndToken) {
            return response([
                "error_message" => "Invalid Credentials!"
            ], 401);
        }

        return response()
            ->json([
                "message" => "login success",
                "user" =>  $userAndToken["user"], "token" => $userAndToken["token"]
            ]);
    }

    public function register(Request $request, AuthService $auth)
    {
        $attrs = $this->validate($request, [
            "name" => "required|string|min:2|max:30",
            "email" => "required|email|unique:users,email",
            "password" => "required|string|min:4|max:30",
            "password_confirmation" => "required|string|same:password"
        ]);

        $attrs["name"] = ucwords($attrs["name"]);

        $user = User::create([
            "name" => $attrs["name"],
            "email" => $attrs["email"],
            "password" => Hash::make($attrs["password"]),
        ]);

        $userAndToken = $auth->attemptAndCreateTokenIfSuccess(
            $attrs["email"],
            $attrs["password"]
        );

        return response()
            ->json([
                "message" => "success registered as {$attrs['name']}",
                "user" =>  $userAndToken["user"], "token" => $userAndToken["token"]
            ]);

        // ->withCookie(Cookie::create("token", $token));
    }

    public function logout()
    {
        return response(["message" => "logout success"])
            ->withCookie(Cookie::create("token", false, 1));
    }

    public function isAuth(Request $request)
    {
        $user = $request->user();
        $token  = $request->user()->token ?? $request->header("token") ?? $request->get("token");

        return ($user && $token) ?
            response()->json([
                "message" => "Authenticated", "user" => $user, "token" => $token,
            ]) : false;
    }
}
