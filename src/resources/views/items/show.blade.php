@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('no-page_title', true)

@section('content')
<div class="item-detail__content">
    {{-- 左：商品画像 --}}
    <div class="item-detail__image">
        @if($item->status == 1)
            <div class="sold-label sold-label-lg">Sold</div>
        @endif
        <img src="{{ asset('storage/' . $item->item_image) }}" alt="商品画像">
    </div>

    {{-- 右：商品情報 --}}
    <div class="item-detail__info">

        <h1 class="item-detail__title">{{ $item->name }}</h1>
        <p class="item-detail__brand">{{ $item->brand }}</p>

        <p class="item-detail__price">¥{{ number_format($item->price) }}<span>（税込）</span></p>

        <div class="item-detail__actions">
            {{-- ハートicon --}}
            <div class="item-action-item">
                <img src="{{ $item->favorited ? asset('images/common/ハートロゴ_ピンク.png') : asset('images/common/ハートロゴ_デフォルト.png') }}" alt="favorite" class="item-favorite" id="favorite-icon" data-item-id="{{ $item->id }}"
                data-default="{{ asset('images/common/ハートロゴ_デフォルト.png') }}"
                data-active="{{ asset('images/common/ハートロゴ_ピンク.png') }}">
                <span class="item-count" id="favorite-count">{{ $item->favorites_count }}</span>
            </div>

            {{-- コメントicon --}}
            <div class="item-action-item">
                <img src="{{ asset('images/common/ふきだしロゴ.png') }}" alt="bubble" class="item-bubble">
                <span class="item-count" id="bubble-count">{{ $item->comments->count() }}</span>
            </div>
        </div>

        <div >
            @if($item->status == 1)
                <a class="purchase-btn" style="background-color: #ccc; pointer-events: none; cursor: not-allowed;">購入手続きへ</a>
            @else
                <a href="{{ route('purchase.create', ['item_id' => $item->id]) }}" class="purchase-btn">購入手続きへ</a>
            @endif
        </div>

        <h2 class="item-detail__heading">商品説明</h2>
        <div>{{ $item->description }}</div>

        <h2 class="item-detail__heading">商品の情報</h2>
        <div class="item-detail__meta">
            <div class="item-detail__meta-group">
                <p>カテゴリー</p>
                <div class ="item-detail__tags">
                    @foreach($item->categories as $category)
                        <span class="label">{{ $category->name }}</span>
                    @endforeach
                </div>
            </div>
            <div class="item-detail__meta-group">
                <p>商品の状態</p>
                <span class="condition-text">{{ $item->condition->name }}</span>
            </div>
        </div>

        <h2 class="item-detail__heading comment-title">
            コメント({{ $item->comments->count() }})
        </h2>
        <div class="comment-list">
            @foreach($item->comments as $comment)
                <div class="comment__content">
                    <div class="comment__user">
                        <div class="comment__avatar">
                            @if($comment->user->profile && $comment->user->profile->profile_image)
                                <img src="{{ asset('storage/' . $comment->user->profile->profile_image) }}" alt="プロフィール画像">
                            @else
                                <div class="comment__avatar-default"></div>
                            @endif
                        </div>
                        <p class="comment__name">{{ $comment->user->name }}</p>
                    </div>
                    <p class="comment__body">{{ $comment->comment }}</p>
                </div>
            @endforeach

            @if($item->comments->isEmpty())
                <div class="comment__content">
                    <p class="comment__body" style="color: #999;">まだコメントはありません。</p>
                </div>
            @endif
        </div>

        <h2 class="comment-form__heading">商品へのコメント</h2>
        <form action="{{ route('comment', ['item_id' => $item->id]) }}" method="POST">
            @csrf
            <textarea name="comment" class="comment-form">{{ old('comment') }}</textarea>

            @error('comment')
                <p class="form-error">{{ $message }}</p>
            @enderror

            @if(session('message'))
                <p style="color: green; font-size: 0.8rem; margin-top: 5px;">{{ session('message') }}</p>
            @endif

            <button type="submit" class="comment-submit">コメントを送信する</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const icon = document.getElementById('favorite-icon');
    const countElement = document.getElementById('favorite-count');

    if (!icon) return;

    icon.addEventListener('click', () => {

        const itemId = icon.dataset.itemId;

        fetch(`{{ url('/item') }}/${itemId}/favorite`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (res.status === 401) {
                window.location.href = "{{ route('login') }}";
                return;
            }
            return res.json();
        })
        .then(data => {
            countElement.innerText = data.count;

            if (data.status === 'added') {
                icon.src = icon.dataset.active;
            } else {
                icon.src = icon.dataset.default;
            }

        })
        .catch(err => console.error('error:', err));
    });
});
</script>
@endpush