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

        // メール認証でカスタム通知を使用する（ここで設定）
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
                return null; // 認証失敗
            }

            // メール認証が未完了の場合、最初のリクエスト時にメール送信
            if (is_null($user->email_verified_at) && !$request->session()->has('verification_mail_sent')) {
                $user->sendEmailVerificationNotification();
                $request->session()->put('verification_mail_sent', true); // メール送信済みフラグを設定
            }

            return $user; // 認証成功
        });

        // FortifyServiceProvider.php

        app()->singleton(
            \Laravel\Fortify\Contracts\LoginResponse::class,
            function () {
                return new class implements \Laravel\Fortify\Contracts\LoginResponse {
                    public function toResponse($request)
                    {
                        if (!$request->user()->hasVerifiedEmail()) {
                            return Redirect::to('/email/verify'); // メール未認証なら認証ページへ
                        }

                        return Redirect::to('/'); // メール認証済みならトップページへ
                    }
                };
            }
        );
    }
}
