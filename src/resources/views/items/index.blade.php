@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('no-page_title', true)

@section('content')
<div class="item-list__content">

    {{-- タブ --}}
    <div class="item-tabs">
        <div class="item-tabs-inner">
            <a href="{{ route('items.index', ['tab' => 'recommend', 'keyword' => request('keyword')]) }}" class="item-tab {{ $tab === 'recommend' ? 'is-active' : '' }}">おすすめ</a>
            <a href="{{ route('items.index', ['tab' => 'mylist', 'keyword' => request('keyword')]) }}" class="item-tab {{ $tab === 'mylist' ? 'is-active' : '' }}">マイリスト</a>
        </div>
    </div>

    {{-- 商品一覧 --}}
    <div id="product-container" class="item-list-grid">
        @include('items._list')
    </div>
</div>
@endsection