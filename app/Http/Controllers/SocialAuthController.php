<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SocialAuthController extends Controller
{
    public function redirectToGoogle() {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback() {
        $user = Socialite::driver('google')->user();
        $this->loginOrRegisterUser($user);
        return $user->bmi ? redirect()->route('dashboard') : redirect()->route('bmi.form');
    }

    public function redirectToFacebook() {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback() {
        $user = Socialite::driver('facebook')->user();
        $this->loginOrRegisterUser($user);
       return $user->bmi ? redirect()->route('dashboard') : redirect()->route('bmi.form');
    }

    protected function loginOrRegisterUser($socialUser) {
        $user = User::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            ['name' => $socialUser->getName(),
            ]
        );
        Auth::login($user);
        return $user;
    }
}
