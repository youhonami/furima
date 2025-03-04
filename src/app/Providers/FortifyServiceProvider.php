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
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Auth\Notifications\VerifyEmail;
use Laravel\Fortify\Contracts\RegisterResponse;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // メール認証でカスタム通知を使用する
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new \App\Notifications\CustomVerifyEmail())->toMail($notifiable);
        });

        // ログインリクエストの制限
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());
            return Limit::perMinute(5)->by($throttleKey);
        });

        // 登録ビューの設定
        Fortify::registerView(function () {
            return view('auth.register');
        });

        // ログインビュー
        Fortify::loginView(function () {
            return view('auth.login');
        });

        // ログイン時のカスタム認証処理
        Fortify::authenticateUsing(function (Request $request) {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $validated['email'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return null;
            }

            // メール認証が未完了の場合、最初のリクエスト時にメール送信
            if (is_null($user->email_verified_at) && !$request->session()->has('verification_mail_sent')) {
                $user->sendEmailVerificationNotification();
                $request->session()->put('verification_mail_sent', true);
            }

            return $user;
        });

        app()->singleton(
            \Laravel\Fortify\Contracts\LoginResponse::class,
            function () {
                return new class implements \Laravel\Fortify\Contracts\LoginResponse {
                    public function toResponse($request)
                    {
                        if (!$request->user()->hasVerifiedEmail()) {
                            return redirect('/email/verify'); // ✅ 確実にリダイレクトする
                        }
                        return redirect('/');
                    }
                };
            }
        );
        app()->singleton(
            RegisterResponse::class,
            function () {
                return new class implements RegisterResponse {
                    public function toResponse($request)
                    {
                        return redirect('/email/verify'); // ✅ 登録後に /email/verify にリダイレクト
                    }
                };
            }
        );
    }
}
