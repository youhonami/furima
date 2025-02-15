<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 認証済みかつメールが未認証の場合、メール認証ページにリダイレクト
        if (Auth::check() && is_null(Auth::user()->email_verified_at)) {
            return redirect()->route('verification.notice')->with('message', 'メールアドレスの確認が必要です。');
        }

        return $next($request);
    }
}
