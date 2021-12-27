<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function PHPUnit\Framework\returnSelf;

class AuthService
{
    public function attemptAndCreateTokenIfSuccess(string $email, string  $password)
    {
        $user  = User::where("email", $email)->first();
        if (!$user) return false;

        $isPasswordMatch  = Hash::check($password, $user->password);

        if (!$isPasswordMatch)  return false;

        return  ["user" => $user, "token" => $user->createToken()];
    }
}
