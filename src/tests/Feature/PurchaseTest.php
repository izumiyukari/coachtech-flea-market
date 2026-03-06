<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 商品購入機能
     * ・「購入する」ボタンを押下 → 購入が完了する
     */
    public function test_user_can_complete_purchase()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => '0']);

        $this->actingAs($user)->withSession([
            'item_id'        => $item->id,
            'postal_code'    => '123-4567',
            'address'        => '東京都...',
            'building'       => 'テストビル',
            'payment_method' => '1',
        ]);

        $response = $this->get(route('checkout.success'));

        $response->assertStatus(302);
        $response->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        $this->assertEquals('1', $item->fresh()->status);
    }

    /**
     * ・購入処理後、商品一覧画面を表示 → 購入した商品はSoldと表示
     */
    public function test_purchased_item_is_displayed_as_sold_on_index()
    {
        $item = Item::factory()->create(['name' => '売り切れの商品', 'status' => '1']);

        $response = $this->get(route('items.index'));

        $response->assertStatus(200);
        $response->assertSee('売り切れの商品');
        $response->assertSee('Sold');
    }

    /**
     * ・購入処理後、プロフィール画面を表示 → プロフィールの購入した商品一覧画面に追加されている
     */
    public function test_purchased_item_is_added_to_profile_buy_list()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['name' => '自分が買った商品']);

        Order::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'postal_code' => '123-4567',
            'address' => '東京都世田谷区',
            'payment_method' => '1',
        ]);

        $response = $this->actingAs($user)->get(route('profile.index', ['page' => 'buy']));

        $response->assertStatus(200);
        $response->assertSee('自分が買った商品');
    }

    /**
     * 支払い方法選択機能
     * ・支払い方法を選択 → 小計画面に支払い方法が反映されている
     */
    public function test_payment_method_is_reflected_in_view()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->get(route('purchase.create', [
            'item_id' => $item->id,
            'payment_method' => '1'
        ]));

        $response->assertStatus(200);
        $response->assertSee('コンビニ払い');
    }

    /**
     * 配送先変更機能
     * ・送付先住所変更画面で住所を登録後、購入画面へ戻る → 登録した住所が商品購入画面に反映
     */
    public function test_updated_address_is_reflected_on_purchase_screen()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $newAddress = [
            'postal_code' => '123-4567',
            'address' => '東京都新宿区1-1-1',
            'building' => 'テストビル101'
        ];

        $this->actingAs($user)->post(route('purchase.address.update', ['item_id' => $item->id]), $newAddress);

        $response = $this->get(route('purchase.create', ['item_id' => $item->id]));

        $response->assertStatus(200);
        $response->assertSee('123-4567');
        $response->assertSee('東京都新宿区1-1-1');
        $response->assertSee('テストビル101');
    }

    /**
     * ・住所変更画面で住所を変更後、購入処理 → 変更した住所が送付先住所に紐づいている
     */
    public function test_purchased_item_is_linked_with_shipping_address()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user)->withSession([
            'item_id'        => $item->id,
            'postal_code'    => '999-0000',
            'address'        => '大阪府大阪市2-2-2',
            'building'       => '購入時ビル',
            'payment_method' => '1',
        ])->get(route('checkout.success'));

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'postal_code' => '999-0000',
            'address' => '大阪府大阪市2-2-2',
        ]);
    }
}
