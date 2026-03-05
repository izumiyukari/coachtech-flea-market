<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;


// 商品一覧（トップ画面）
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// 商品詳細
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');

// ログイン必須のルート
Route::middleware('auth')->group(function () {

    // 認証案内画面
    Route::get('/email/verify', fn() => view('auth.verify-email'))->name('verification.notice');

    // 認証メール再送
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back();
    })->middleware(['throttle:6,1'])->name('verification.send');

    // 認証リンククリック後
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('profile.edit')->with('status', 'verification-success');
    })->middleware(['signed'])->name('verification.verify');

    // メール認証必須のルート
    Route::middleware('verified')->group(function () {

        // マイページ画面
        Route::get('/mypage', [ProfileController::class, 'index'])->name('profile.index');

        // プロフィール編集画面
        Route::get('/mypage/profile', [ProfileController::class, 'editProfile'])->name('profile.edit');

        // プロフィール更新処理
        Route::post('/mypage/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');

        // 出品画面
        Route::get('/sell', [ItemController::class, 'create'])->name('items.create');

        // 出品処理
        Route::post('/sell', [ItemController::class, 'store'])->name('items.store');

        // 購入画面
        Route::get('/purchase/{item_id}', [PurchaseController::class, 'create'])->name('purchase.create');

        // 購入処理
        Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.store');

        // Stripe決済処理
        Route::get('/checkout/success', [PurchaseController::class, 'success'])->name('checkout.success');

        // 住所変更画面
        Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');

        // 住所更新処理
        Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

        // いいねの登録・解除
        Route::post('/item/{item_id}/favorite', [ItemController::class, 'favorite'])->name('favorite');

        // コメント登録処理
        Route::post('/item/{item_id}/comment', [ItemController::class, 'comment'])->name('comment');

        // 画像のプレビュー表示
        Route::post('/mypage/profile/image-preview', [ProfileController::class, 'updateImage'])->name('profile.image.preview');
    });
});
