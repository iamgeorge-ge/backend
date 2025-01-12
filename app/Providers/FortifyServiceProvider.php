<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        # Authentication
        Fortify::loginView(function () {
            return view('auth.login');
        });

        # Authenticating With Two Factor Authentication
        Fortify::twoFactorChallengeView(function () {
            return view('auth.two-factor-challenge');
        });

        # Register
        Fortify::registerView(function () {
            return view('auth.register');
        });

        # Password Reset
        Fortify::requestPasswordResetLinkView(function () {
            return view('auth.forgot-password');
        });

        # Resetting the Password
        Fortify::resetPasswordView(function (Request $request) {
            return view(
                'auth.reset-password',
                ['request' => $request]
            );
        });

        # Email Verification
        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });

        #Password Confirmation
        Fortify::confirmPasswordView(function () {
            return view('auth.confirm-password');
        });

        # 2FA Challenge View
        Fortify::twoFactorChallengeView(function (Request $request) {
            $recovery = $request->get('recovery', false);
            return view('auth.two-factor-challenge', compact('recovery'));
        });


        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
