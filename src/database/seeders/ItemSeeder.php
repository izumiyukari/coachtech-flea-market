<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 商品データのリスト
        $items = [
            [
                'condition_id' => 1,
                'name' => '腕時計',
                'categories' => ['ファッション', 'メンズ'],
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'brand' => 'Rolax',
                'item_image' => 'item_images/Armani+Mens+Clock.jpg',
            ],
            [
                'condition_id' => 2,
                'name' => 'HDD',
                'categories' => ['家電'],
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'brand' => '西芝',
                'item_image' => 'item_images/HDD+Hard+Disk.jpg',
            ],
            [
                'condition_id' => 3,
                'name' => '玉ねぎ3束',
                'categories' => ['キッチン'],
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'brand' => 'なし',
                'item_image' => 'item_images/iLoveIMG+d.jpg',
            ],
            [
                'condition_id' => 4,
                'name' => '革靴',
                'categories' => ['ファッション', 'メンズ'],
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'brand' => null,
                'item_image' => 'item_images/Leather+Shoes+Product+Photo.jpg',
            ],
            [
                'condition_id' => 1,
                'name' => 'ノートPC',
                'categories' => ['家電', 'インテリア'],
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'brand' => null,
                'item_image' => 'item_images/Living+Room+Laptop.jpg',
            ],
            [
                'condition_id' => 2,
                'name' => 'マイク',
                'categories' => ['家電'],
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'brand' => 'なし',
                'item_image' => 'item_images/Music+Mic+4632231.jpg',
            ],
            [
                'condition_id' => 3,
                'name' => 'ショルダーバッグ',
                'categories' => ['ファッション', 'レディース'],
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'brand' => null,
                'item_image' => 'item_images/Purse+fashion+pocket.jpg',
            ],
            [
                'condition_id' => 4,
                'name' => 'タンブラー',
                'categories' => ['インテリア', 'キッチン'],
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'brand' => 'なし',
                'item_image' => 'item_images/Tumbler+souvenir.jpg',
            ],
            [
                'condition_id' => 1,
                'name' => 'コーヒーミル',
                'categories' => ['インテリア','キッチン'],
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'brand' => 'Starbacks',
                'item_image' => 'item_images/Waitress+with+Coffee+Grinder.jpg',
            ],
            [
                'condition_id' => 2,
                'name' => 'メイクセット',
                'categories' => ['レディース', 'コスメ'],
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'brand' => null,
                'item_image' => 'item_images/外出メイクアップセット.jpg',
            ],
        ];

        foreach ($items as $itemData) {
            $item = Item::create([
                'condition_id'  => $itemData['condition_id'],
                'name'          => $itemData['name'],
                'price'         => $itemData['price'],
                'description'   => $itemData['description'],
                'brand'         => $itemData['brand'],
                'item_image' => $itemData['item_image'],
                'user_id' => User::inRandomOrder()->value('id'),   // ランダムな既存ユーザー
                'status' => 0,    // 0:on_sale(出品中) 1:sold(売切)
            ]);

            // カテゴリー情報が配列にあれば紐付け
            if (isset($itemData['categories'])) {
                $categoryIds = Category::whereIn('name', $itemData['categories'])->pluck('id');
                $item->categories()->attach($categoryIds);
            }
        }
    }
}
