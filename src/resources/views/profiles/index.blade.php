@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('no-page_title', true)

@section('content')
<div class="mypage">
    {{-- プロフィールヘッダー --}}
    <div class="mypage__header">
        <div class="mypage__user">
            <div class="mypage__icon">
            @if($profile?->profile_image)
                <img src="{{ $profile?->image_url }}">
            @endif
            </div>
            <div class="mypage__name">{{ $user->name }}</div>
        </div>
        <a href="{{ route('profile.edit') }}" class="mypage__edit">
            プロフィールを編集
        </a>
    </div>


    {{-- タブ --}}
    <div class="item__tabs">
        <div class="item-tabs-inner">
            <a href="{{ route('profile.index', ['page' => 'sell', 'keyword' => request('keyword')]) }}" class="item-tab {{ $page === 'sell' ? 'is-active' : '' }}">出品した商品</a>
            <a href="{{ route('profile.index', ['page' => 'buy', 'keyword' => request('keyword')]) }}" class="item-tab {{ $page === 'buy' ? 'is-active' : '' }}">購入した商品</a>
        </div>
    </div>

    {{-- 商品一覧 --}}
    <div id="mypage-container" class="mypage__items">
        @include('items._list')
    </div>
</div>
@endsection