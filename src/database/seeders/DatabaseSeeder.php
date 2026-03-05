<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // テストログイン用
        User::factory()->hasProfile([
            'postal_code'   => '123-4567',
            'address'       => '東京都渋谷区...',
            'building'      => 'テストビル101',
            'profile_image' => 'sample.jpg',
        ])->create([
            'name' => 'テスト太郎',
            'email' => 'test1@example.com',
            'password' => bcrypt('test1111'),
        ]);

        // ダミーユーザー量産
        User::factory(2)->create();

        $this->call([
            CategorySeeder::class,
            ConditionSeeder::class,
            ItemSeeder::class,
        ]);
    }
}