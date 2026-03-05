<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;
use App\Models\Order;
use App\Models\Profile;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    /* プロフィール画面 */
    public function index(Request $request)
    {
        $user = auth()->user();
        $page = $request->query('page', 'sell');
        $keyword = $request->query('keyword');
        $profile = $user->profile;

        if ($page === 'sell') {

            // 出品した商品を取得
            $items = Item::where('user_id', $user->id)
                ->when($keyword, fn($q) =>$q->where('name', 'LIKE', "%{$keyword}%"))
                ->get();

        } else {

            // 購入した商品を取得
            $items = Item::whereHas('order', function ($q) use ($user) {$q->where('user_id', $user->id);})
                ->when($keyword, fn($q) => $q->where('name', 'LIKE', "%{$keyword}%"))
                ->get();
        }

        // タブが押下されている時、商品一覧だけのHTMLを返す
        if ($request->ajax()) {
            return view('items._list', compact('items'))->render();
        }
        return view('profiles.index', compact('items','user','page','profile'));
    }

    /* プロフィール編集画面 */
    public function editProfile()
    {
        $user = Auth::user();
        $profile = $user->profile;

        return view('profiles.edit', compact('user','profile'));
    }

    /* プロフィール更新処理 */
    public function updateProfile(ProfileRequest $request)
    {
        $user = Auth::user();
        // 空のインスタンスを作成
        $profile = $user->profile ?: new Profile();
        $profile->user_id = $user->id;

         // チェック済みのデータを受け取る
        $validated = $request->validated();

        // 画像処理
        if ($request->hasFile('profile_image')) {

            if ($profile->profile_image) {
                Storage::disk('public')->delete($profile->profile_image);
            }
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $validated['profile_image'] = $path;

        } else {

            unset($validated['profile_image']);
        }

        // ユーザー情報更新
        $user->update(['name' => $validated['name'],]);
        $profile->fill($validated)->save();

        return redirect()->route('items.index');
    }

    /* 画像アップデート時処理 */
    public function updateImage(ProfileRequest $request)
    {
        // JSでプレビュー表示
        return response()->json(['success' => true]);
    }
}