<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class UserController extends Controller
{
    public function show()
    {
        $user = Auth::user(); // ログイン中のユーザー情報を取得
        return view('mypage', compact('user'));
    }

    public function index()
    {
        $user = Auth::user();
        $items = Item::where('user_id', $user->id)->get(); // ログインユーザーの商品を取得

        return view('mypage', compact('user', 'items'));
    }
}
