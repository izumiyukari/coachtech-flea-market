<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ユーザ情報取得
     * ・ログイン後、プロフィールページを開く → プロフィール画像、ユーザ名、出品・購入した商品一覧が正しく表示
     */
    public function test_user_can_view_profile_info_and_item_lists()
    {
        $user = User::factory()->create(['name' => 'テストユーザー',]);
        Profile::factory()->create([
            'user_id' => $user->id,
            'profile_image' => 'profile.jpg',
        ]);

        $sellItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '出品した商品'
        ]);

        $buyItem = Item::factory()->create(['name' => '購入した商品']);
        Order::factory()->create([
            'user_id' => $user->id,
            'item_id' => $buyItem->id,
        ]);

        $response = $this->actingAs($user)->get(route('profile.index'));
        $response->assertStatus(200);

        $response->assertSee('テストユーザー');
        $response->assertSee('profile.jpg');
        $response->assertSee('出品した商品');

        $buyResponse = $this->get(route('profile.index', ['tab' => 'buy']));
        $buyResponse->assertSee('購入した商品');
    }

    /**
     * ユーザ情報変更
     * ・ユーザにログイン、プロフィール編集ページを開く → 各項目の初期値が正しく表示されている
     */
    public function test_user_can_update_profile_info()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $image = UploadedFile::fake()->image('new_avatar.jpg');

        $updateData = [
            'name' => '新しい名前',
            'profile_image' => $image,
            'postal_code' => '111-2222',
            'address' => '東京都渋谷区',
            'building' => 'ABCビル',
        ];

        $response = $this->actingAs($user)->post(route('profile.update'), $updateData);

        $response->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => '新しい名前',
        ]);

        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'postal_code' => '111-2222',
            'address' => '東京都渋谷区',
            'building' => 'ABCビル',
            'profile_image' => 'profile_images/' . $image->hashName(),
        ]);

        $updatedUser = $user->refresh();
        Storage::disk('public')->assertExists('profile_images/' . $updatedUser->profile_image);
    }
}
