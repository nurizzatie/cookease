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
        $socialUser = Socialite::driver('google')->user();
        $user = $this->loginOrRegisterUser($socialUser);
        
        return $user->bmi ? redirect()->route('dashboard') : redirect()->route('bmi.form');
    }

    public function redirectToFacebook() {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback() {
        $socialUser = Socialite::driver('facebook')->user();
        $user = $this->loginOrRegisterUser($socialUser);
        

       return $user->bmi ? redirect()->route('dashboard') : redirect()->route('bmi.form');
    }

    protected function loginOrRegisterUser($socialUser) {
        $user = User::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            ['name' => $socialUser->getName(),
            ]
        );
        Auth::login($user);
        return $user->load('bmi');
    }
}
