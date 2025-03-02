<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConditionsTableSeeder extends Seeder
{
    public function run()
    {
        // 🚀 外部キー制約を無効化
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 🚀 `conditions` テーブルをクリア
        DB::table('conditions')->truncate();

        // 🚀 外部キー制約を再有効化
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // データを再挿入
        DB::table('conditions')->insert([
            ['condition' => '良好'],
            ['condition' => '目立った傷や汚れなし'],
            ['condition' => 'やや傷や汚れあり'],
            ['condition' => '状態が悪い'],
        ]);
    }
}
