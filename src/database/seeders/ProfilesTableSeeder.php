<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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
        // ユーザー1のプロフィール
        Profile::create([
            'user_id' => 1, // ユーザー1のID
            'img' => 'profile_images/grapes.png',  // ユーザー1の画像
            'postal_code' => '111-1111',
            'address' => '東京都新宿区2-2-2',
            'building' => '新宿タワー',
        ]);

        // ユーザー2のプロフィール
        Profile::create([
            'user_id' => 2, // ユーザー2のID
            'img' => 'profile_images/banana.png',  // ユーザー2の画像
            'postal_code' => '222-2222',
            'address' => '東京都千代田区3-3-3',
            'building' => '千代田ビル',
        ]);

        // ユーザー3のプロフィール
        Profile::create([
            'user_id' => 3, // ユーザー3のID
            'img' => 'profile_images/kiwi.png',
            'postal_code' => '333-3333',
            'address' => '東京都港区4-4-4',
            'building' => '港タワー',
        ]);
    }
}
