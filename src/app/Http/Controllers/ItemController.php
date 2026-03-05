<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Favorite;
use App\Models\Comment;
use App\Http\Requests\ItemRequest;
use App\Http\Requests\CommentRequest;


class ItemController extends Controller
{
    /* 商品一覧画面 */
    public function index(Request $request)
    {
        $keyword = $request->query('keyword');   // 検索ワードを取得
        $tab = $request->query('tab');   // タブの状態チェック
        $query = Item::with(['favorites']);   // DBに対して検索条件を組み立てる準備

        // 検索キーワードによる絞り込み
        $query->when($keyword, fn($q) => $q->where('name', 'LIKE', "%{$keyword}%"));

        // ログインチェック
        if (auth()->check()){
            if ($tab === 'mylist') {
                // ログイン時、マイリストにユーザーのお気に入りを表示
                $query->whereHas('favorites', fn($q) => $q->where('user_id', auth()->id()));

            } else {
                // ログイン時、おすすめには自分が出品した商品以外を表示
                $query->where('user_id', '!=', auth()->id());
            }
        // 未ログインの場合
        } else {

            // マイリスト選択時、検索結果を表示しない
            if ($tab === 'mylist') {
                $query->whereRaw('1 = 0');
            }
        }
        $items = $query->get();

        // タブが押下されている時、商品一覧だけのHTMLを返す
        if ($request->ajax()) {
            return view('items._list', compact('items'))->render();
        }

        return view('items.index', compact('items','tab'));
    }

    /* 商品詳細画面 */
    public function show($item_id)
    {
        // ユーザがいいねしているか
        $favorited = ['favorites as favorited' => function($query) {
            $query->where('user_id', auth()->id());
        }];

        $item = Item::with('categories', 'condition','comments','favorites')
        ->withCount('favorites')
        ->withExists($favorited)
        ->findOrFail($item_id);

        return view('items.show', compact('item'));
    }

    /* いいね処理 */
    public function favorite($item_id)
    {
        $user_id = auth()->id();
        $favorite = Favorite::where('user_id', $user_id)
            ->where('item_id', $item_id)
            ->first();

        if ($favorite) {
            // いいね解除
            $favorite->delete();
            $status = 'removed';
        } else {
            // いいね登録
            Favorite::create([
                'user_id' => $user_id,
                'item_id' => $item_id,
            ]);
            $status = 'added';
        }
        // いいね数取得
        $count = Favorite::where('item_id', $item_id)->count();

        return response()->json([
            'status' => $status,
            'count'  => $count,
        ]);
    }

    /* 画像アップデート時処理 */
    public function updateImage(ItemRequest $request)
    {
        // JSでプレビュー表示
        return response()->json(['success' => true]);
    }

    /* コメント処理 */
    public function comment(CommentRequest $request, $item_id)
    {
       // コメント保存
        auth()->user()->comments()->create([
            'item_id' => $item_id,
            'comment' => $request->comment,
        ]);
        // コメント数取得
        $count = Comment::where('item_id', $item_id)->count();

        return back()->with(['comment_count' => $count]);
    }

    /* 商品出品画面 */
    public function create()
    {
        $categories = Category::all();
        $conditions = Condition::all();

        return view('items.create', compact('categories', 'conditions'));
    }

    /* 出品登録処理 */
    public function store(ItemRequest $request)
    {
        $path = $request->file('item_image')->store('item_images', 'public');

        // itemsテーブル保存
        $item = Item::create([
            'user_id'     => auth()->id(),
            'condition_id'=> $request->condition_id,
            'name'        => $request->name,
            'description' => $request->description,
            'brand'       => $request->brand,
            'item_image'  => $path,
            'price'       => $request->price,
        ]);

        // カテゴリー保存
        $item->categories()->sync($request->category_id);

        return redirect()->route('items.index');
    }
}
