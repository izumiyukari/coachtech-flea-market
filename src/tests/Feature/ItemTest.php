<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 商品一覧取得
     * ・ログインせず、商品ページを開く → すべての商品が表示
     */
    public function test_guest_can_see_all_items()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        Item::factory()->create(['user_id' => $userA->id, 'name' => '商品A']);
        Item::factory()->create(['user_id' => $userB->id, 'name' => '商品B']);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('商品A');
        $response->assertSee('商品B');
    }

    /**
     * ・商品ページを開く → 購入済み商品はSoldと表示
     */
    public function test_purchased_items_display_sold()
    {
        $item = Item::factory()->create([
            'name' => '購入済み商品',
            'status' => '1',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('購入済み商品');
        $response->assertSee('Sold');
    }

    /**
     * ・ログイン後、商品ページを開く → 自分が出品した商品が一覧に表示されない
     */
    public function test_logged_in_user_cannot_see_own_items()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Item::factory()->create(['user_id' => $otherUser->id, 'name' => '他人の商品']);
        Item::factory()->create(['user_id' => $user->id, 'name' => '自分の商品']);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('他人の商品');
        $response->assertDontSee('自分の商品');
    }

    /**
     * マイリスト一覧取得
     * ・ログイン後、マイリストを開く → いいねした商品だけが表示
     */
    public function test_mylist_displays_only_favorited_items()
    {
        $user = User::factory()->create();
        $item1 = Item::factory()->create(['name' => 'いいねした商品']);
        $item2 = Item::factory()->create(['name' => 'いいねしてない商品']);

        $user->favorites()->create(['item_id' => $item1->id]);

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('いいねした商品');
        $response->assertDontSee('いいねしてない商品');
    }

    /**
     * ・ログイン後、マイリストの購入済み商品を確認 → 購入済み商品にSOLDと表示
     */
    public function test_purchased_items_display_sold_in_mylist()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'name' => '購入済み商品',
            'status' => '1',
        ]);

        $user->favorites()->create(['item_id' => $item->id]);

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('購入済み商品');
        $response->assertSee('Sold');
    }

    /**
     * ・ログインせず、マイリストを表示 → 何も表示されない
     */
    public function test_mylist_displays_nothing_for_guest()
    {
        Item::factory()->create(['name' => 'テスト商品']);

        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertDontSee('テスト商品');
    }

    /**
     * 商品検索機能
     * ・検索欄にキーワード入力 → 商品名で部分一致検索ができる
     */
    public function test_search_by_item_name()
    {
        Item::factory()->create(['name' => '腕時計']);
        Item::factory()->create(['name' => 'タンブラー']);

        $response = $this->get('/?keyword=時計');

        $response->assertStatus(200);
        $response->assertSee('腕時計');
        $response->assertDontSee('タンブラー');
    }

    /**
    * ・検索欄にキーワード入力（マイリストページに遷移） → 検索キーワードが保持
    */
    public function test_search_keyword_is_retained_on_mylist_tab()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['name' => '腕時計']);
        $user->favorites()->create(['item_id' => $item->id]);

        $response = $this->actingAs($user)->get('/?tab=mylist&keyword=時計');

        $response->assertStatus(200);
        $response->assertSee('腕時計');

        $response->assertSee('value="時計"', false);
    }

    /**
     * 出品商品情報登録
     * ・商品出品画面にて各項目に適切な情報を入力 → 各項目が正しく保存されている
     */
    public function test_user_can_create_item_with_valid_data()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $category = Category::first() ?? Category::create(['name' => 'ファッション']);
        $condition = Condition::first() ?? Condition::create(['name' => '良好']);

        $image = UploadedFile::fake()->image('item.jpg');

        $itemData = [
            'category_id' => $category->id,
            'condition_id' => $condition->id,
            'name' => 'テスト商品名',
            'brand' => 'テストブランド',
            'description' => '商品の説明文です。',
            'price' => 5000,
            'item_image' => $image,
        ];

        $response = $this->actingAs($user)->post(route('items.store'), $itemData);
        $response->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'name' => 'テスト商品名',
            'brand' => 'テストブランド',
            'description'  => '商品の説明文です。',
            'price' => 5000,

        ]);
        $this->assertDatabaseHas('category_item', [
            'category_id' => $category->id,
        ]);
        Storage::disk('public')->assertExists('item_images/' . $image->hashName());
    }
}

