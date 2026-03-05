<?php
namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Profile;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\AddressRequest;

class PurchaseController extends Controller
{
   /* 購入画面 */
    public function create($item_id)
    {
        $item = $this->getItem($item_id);
        $profile = Profile::where('user_id', auth()->id())->first();

        return view('purchase.create', compact('item','profile'));
    }

    /* 購入処理 */
    public function store(OrderRequest $request, $item_id)
    {
        $validated = $request->validated();

        $item = $this->getItem($item_id);

        // セッションに購入情報を一時保存
        session([
            'item_id'        => $item->id,
            'postal_code'    => $validated['postal_code'],
            'address'        => $validated['address'],
            'building'       => $validated['building'],
            'payment_method' => $validated['payment_method'],
        ]);
        session()->save();

        // Stripeセッションの作成
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => ['name' => $item->name,],
                    'unit_amount' => $item->price,],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('checkout.success'),
            'cancel_url' => route('items.show', $item->id),
        ]);

        // Stripeの決済ページへリダイレクト
        return redirect($session->url);
    }

    /* 決済処理 */
    public function success()
    {
        // 二重購入防止などのため、トランザクション開始
        DB::transaction(function () {
            // 購入履歴を作成
            Order::create([
                'user_id' => auth()->id(),
                'item_id' => session('item_id'),
                'postal_code'    => session('postal_code'),
                'address'        => session('address'),
                'building'       => session('building'),
                'payment_method' => session('payment_method'),
            ]);
            Item::find(session('item_id'))->update(['status' => 1]);
        });
        return redirect()->route('items.index');
    }

    /* 住所変更画面 */
    public function editAddress($item_id)
    {
        $item = $this->getItem($item_id);
        $profile = auth()->user()->profile;

        return view('purchase.address', compact('item','profile'));
    }

    /* 住所変更処理 */
    public function updateAddress(AddressRequest $request, $item_id)
    {
        // チェック済みのデータを受け取る
        $validated = $request->validated();

        // 保存処理
        auth()->user()->profile()->updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'postal_code' => $validated['postal_code'],
                'address'     => $validated['address'],
                'building'    => $validated['building'],
            ]
        );
        return redirect()->route('purchase.create', ['item_id' => $item_id]);
    }

    // 共通:商品情報取得メソッド
    private function getItem($item_id)
    {
        return Item::findOrFail($item_id);
    }
}
