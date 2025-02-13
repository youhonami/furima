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

            if (!$user->hasVerifiedEmail()) {
                // メール認証が未完了の場合、認証メールを再送信
                $user->sendEmailVerificationNotification();
                throw ValidationException::withMessages([
                    'email' => ['メールアドレスの確認が必要です。認証メールをご確認ください。'],
                ]);
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
