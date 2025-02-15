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
use Illuminate\Support\Facades\Route; // 追加
use Illuminate\Support\Facades\URL;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Auth\Notifications\VerifyEmail;

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

        Fortify::authenticateUsing(function (Request $request) {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $validated['email'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return null; // 認証失敗
            }

            // 毎回メール認証を再送信
            $user->sendEmailVerificationNotification();

            return $user; // 認証成功


            // メール認証でカスタム通知を使用する
            VerifyEmail::toMailUsing(function ($notifiable, $url) {
                return (new CustomVerifyEmail)->toMail($notifiable);
            });
        });



        // 登録後のリダイレクト先を認証待ちページにカスタマイズ
        app()->singleton(
            \Laravel\Fortify\Contracts\RegisterResponse::class,
            function () {
                return new class implements \Laravel\Fortify\Contracts\RegisterResponse {
                    public function toResponse($request)
                    {
                        // セッションに登録から来たことを記録
                        session(['from_registration' => true]);

                        // 登録後にメール認証ページへ
                        return Redirect::to('/email/verify');
                    }
                };
            }
        );

        app()->singleton(
            \Laravel\Fortify\Contracts\LoginResponse::class,
            function () {
                return new class implements \Laravel\Fortify\Contracts\LoginResponse {
                    public function toResponse($request)
                    {
                        // ログイン後にメール認証ページへ
                        return Redirect::to('/email/verify');
                    }
                };
            }
        );
    }
}
