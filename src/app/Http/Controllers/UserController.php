<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function show()
    {
        $user = Auth::user(); // ログイン中のユーザー情報を取得
        return view('mypage', compact('user'));
    }
}
