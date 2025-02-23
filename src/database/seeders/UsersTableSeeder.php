<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => '三毛猫',
                'email' => 'qqq@qqq.co.jp',
                'email_verified_at' => null, // 空白に設定
                'password' => Hash::make('aaaaaaaaaa'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'シャムネコ',
                'email' => 'www@www.co.jp',
                'email_verified_at' => null, // 空白に設定
                'password' => Hash::make('aaaaaaaaaa'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ちしゃ猫',
                'email' => 'eee@eee.co.jp',
                'email_verified_at' => null, // 空白に設定
                'password' => Hash::make('aaaaaaaaaa'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
