<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;

class ProfilesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 既存のユーザーに対してプロフィールを作成
        User::all()->each(function ($user) {
            Profile::create([
                'user_id' => $user->id,
                'img' => 'images/banana.png',  // デフォルト画像のパス
                'postal_code' => '123-4567',                    // 仮の郵便番号
                'address' => '東京都渋谷区1-1-1',               // 仮の住所
                'building' => '渋谷ビル',                        // 仮の建物名
            ]);
        });
    }
}
