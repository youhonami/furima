<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 商品データを定義
        $items = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'img' => 'items-img/Armani+Mens+Clock.jpg',
                'condition_id' => 1,
                'categories' => [1, 5],
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'img' => 'items-img/HDD+Hard+Disk.jpg',
                'condition_id' => 2,
                'categories' => [2],
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'img' => 'items-img/iLoveIMG+d.jpg',
                'condition_id' => 3,
                'categories' => [10],
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'img' => 'items-img/Leather+Shoes+Product+Photo.jpg',
                'condition_id' => 4,
                'categories' => [1, 5],
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'img' => 'items-img/Living+Room+Laptop.jpg',
                'condition_id' => 1,
                'categories' => [2],
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'img' => 'items-img/Music+Mic+4632231.jpg',
                'condition_id' => 2,
                'categories' => [2],
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'img' => 'items-img/Purse+fashion+pocket.jpg',
                'condition_id' => 3,
                'categories' => [1, 4],
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'img' => 'items-img/Tumbler+souvenir.jpg',
                'condition_id' => 4,
                'categories' => [10],
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'img' => 'items-img/Waitress+with+Coffee+Grinder.jpg',
                'condition_id' => 1,
                'categories' => [10],
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'img' => 'items-img/makeup+set.jpg',
                'condition_id' => 2,
                'categories' => [6],
            ],
        ];

        // 商品データを挿入し、カテゴリを関連付け
        foreach ($items as $itemData) {
            // categories を分離
            $categories = $itemData['categories'];
            unset($itemData['categories']);

            // items テーブルに商品データを挿入
            $itemId = DB::table('items')->insertGetId(array_merge($itemData, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            // category_item テーブルにカテゴリを関連付け
            foreach ($categories as $categoryId) {
                DB::table('category_item')->insert([
                    'item_id' => $itemId,
                    'category_id' => $categoryId,
                ]);
            }
        }
    }
}
