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
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::loginView(function () {
            return view('auth.login');
        });

        // Fortify のログイン処理前に `LoginRequest` のバリデーションを適用
        Fortify::authenticateUsing(function (Request $request) {
            // `LoginRequest` のバリデーションを適用
            $validated = $request->validate((new LoginRequest())->rules(), (new LoginRequest())->messages());

            // 認証処理
            $user = User::where('email', $validated['email'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return null; // 認証失敗
            }

            return $user; // 認証成功
        });


        // 登録後のリダイレクト先をカスタマイズ
        app()->singleton(
            \Laravel\Fortify\Contracts\RegisterResponse::class,
            function () {
                return new class implements \Laravel\Fortify\Contracts\RegisterResponse {
                    public function toResponse($request)
                    {
                        return Redirect::to('/profile/edit');
                    }
                };
            }
        );
    }
}
