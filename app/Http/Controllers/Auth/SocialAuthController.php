<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the OAuth provider.
     */
    public function redirect(string $provider): RedirectResponse
    {
        $driver = Socialite::driver($provider);

        if ($provider === 'facebook') {
            $driver->scopes(['email', 'user_friends']);
        }

        return $driver->redirect();
    }

    /**
     * Handle the OAuth provider callback.
     */
    public function callback(string $provider): RedirectResponse
    {
        $socialUser = Socialite::driver($provider)->user();

        if (Auth::check() && $provider === 'facebook') {
            $request = request();
            $request->user()->update([
                'facebook_id' => $socialUser->getId(),
                'facebook_token' => $socialUser->token,
            ]);

            return redirect()->route('contacts.index')->with('status', 'Cuenta de Facebook vinculada.');
        }

        $user = $this->findOrCreateUser($provider, $socialUser);

        Auth::login($user, true);

        return redirect()->route('dashboard');
    }

    /**
     * Find an existing user for the given provider or create a new one.
     */
    private function findOrCreateUser(string $provider, SocialiteUser $socialUser): User
    {
        $providerIdColumn = "{$provider}_id";

        $user = User::where($providerIdColumn, $socialUser->getId())->first();

        if ($user) {
            if ($provider === 'facebook') {
                $user->update(['facebook_token' => $socialUser->token]);
            }

            return $user;
        }

        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            $user->update([
                $providerIdColumn => $socialUser->getId(),
                'facebook_token' => $provider === 'facebook' ? $socialUser->token : $user->facebook_token,
                'avatar' => $user->avatar ?? $socialUser->getAvatar(),
            ]);

            return $user;
        }

        return User::create([
            'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Usuario Jalon',
            'email' => $socialUser->getEmail(),
            'password' => null,
            'email_verified_at' => now(),
            $providerIdColumn => $socialUser->getId(),
            'facebook_token' => $provider === 'facebook' ? $socialUser->token : null,
            'avatar' => $socialUser->getAvatar(),
        ]);
    }
}
