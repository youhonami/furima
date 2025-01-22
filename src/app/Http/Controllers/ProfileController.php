<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Profile;

class ProfileController extends Controller
{
    // マイページ表示
    public function show()
    {
        $user = Auth::user();
        return view('mypage', compact('user'));
    }

    // プロフィール編集画面の表示
    public function edit()
    {
        $user = Auth::user(); // 現在ログインしているユーザー情報を取得
        return view('profile', compact('user')); // ビューにユーザー情報を渡す
    }

    // プロフィール更新処理
    public function update(Request $request)
    {
        $user = Auth::user();

        // バリデーション
        $request->validate([
            'name' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 画像のバリデーション
        ]);

        // プロフィール画像の処理
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('profile_images', 'public');

            // 古い画像を削除
            if ($user->profile && $user->profile->img) {
                Storage::disk('public')->delete($user->profile->img);
            }
        } else {
            $profileImagePath = $user->profile->img ?? null;
        }

        // ユーザープロフィールの保存
        $user->update([
            'name' => $request->input('name'),
        ]);

        // プロフィールテーブルの保存
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'postal_code' => $request->input('postal_code'),
                'address' => $request->input('address'),
                'building' => $request->input('building'),
                'img' => $profileImagePath, // プロフィール画像パスを保存
            ]
        );

        return redirect('/')->with('success', 'プロフィールが更新されました。');
    }

    public function editAddress()
    {
        // ユーザー情報を取得
        $user = Auth::user();
        $profile = $user->profile; // ユーザーの住所情報（関連付けされている場合）

        // address.blade.php を表示
        return view('address', compact('profile'));
    }
}
