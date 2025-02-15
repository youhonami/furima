<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        // 現在認証されているユーザーを取得
        $user = Auth::user();

        if ($user) {
            // email_verified_at を null にリセット
            $user->email_verified_at = null;
            $user->save();
        }

        // セッションを無効化してログアウト
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ログアウト後のリダイレクト先（例：ログインページ）
        return redirect('/login');
    }
}
