<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Profile;
use App\Http\Requests\ProfileRequest;

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
        $user = Auth::user();
        return view('profile', compact('user'));
    }

    // プロフィール更新処理
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        // バリデーション
        $request->validate([
            'name' => 'required|',
            'postal_code' => 'required|string|max:10',
            'address' => 'required',
            'building' => 'nullable',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
                'img' => $profileImagePath,
            ]
        );

        return redirect('/')->with('success', 'プロフィールが更新されました。');
    }
}
