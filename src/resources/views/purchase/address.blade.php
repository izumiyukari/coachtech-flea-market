@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('page_title')
<h2 class="page__title">住所の変更</h2>
@endsection


@section('content')
<div class="purchase__content">
    <form class="address__from" method="POST" action="{{ route('purchase.address.update', ['item_id' => $item->id]) }}">
        @csrf
        <div class="address__group">
            <div class="address__content">
                <label class="address__label" for="postal_code">郵便番号</label>
                <input class="address__input" id="postal_code" name="postal_code" type="text" value="{{ old('postal_code', $profile->postal_code ?? '') }}">
                @error('postal_code')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="address__group">
            <div class="address__content">
                <label class="address__label" for="address">住所</label>
                <input class="address__input" id="address" name="address" type="text" value="{{ old('address', $profile->address ?? '') }}">
                @error('address')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="address__group">
            <div class="address__content">
                <label class="address__label" for="building">建物名</label>
                <input class="address__input" id="building" name="building" type="text" value="{{ old('building', $profile->building ?? '') }}">
            </div>
        </div>

        <div >
            <button type="submit" class="update-btn">更新する</button>
        </div>
    </form>
</div>
@endsection
