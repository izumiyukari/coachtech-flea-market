@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('page_title')
@endsection

@section('content')
<div class="purchase__content">
    <form action="{{ route('purchase.store', $item) }}" method="POST" id="purchase-form" class="purchase__form">
        @csrf
        {{-- 左：購入情報 --}}
        <div class="order__info">

            {{-- 商品 --}}
            <div class="order__item">
                <img class="order__image" src="{{ asset('storage/' . $item->item_image) }}" alt="商品画像">
                <div>
                    <h2 class="order__title">{{ $item->name }}</h2>
                    <p class="order__price">¥{{ number_format($item->price) }}</p>
                </div>
            </div>
            {{-- 支払い方法 --}}
            <div class="order__block">
                <h3 class="order__heading">支払い方法</h3>
                <div class="order__select" data-name="payment_method" id="payment-method">
                    <input type="hidden" name="payment_method" id="payment_method_input"
                    value="{{ old('payment_method') }}">
                    <div class="select__trigger">
                        <span class="select__text is-placeholder">選択してください</span>
                        <span class="select__arrow">▼</span>
                    </div>

                    <ul class="select__options">
                        <li  class="option-item" data-value="1">コンビニ払い</li>
                        <li  class="option-item" data-value="2">カード払い</li>
                    </ul>
                    @error('payment_method')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- 配送先 --}}
            <div class="order__block">
                <div class="order__address-header">
                    <h3 class="order__heading">配送先</h3>
                    <a href="{{ route('purchase.address.edit', $item) }}" class="order__address-edit">変更する</a>
                </div>
                <p>〒{{ $profile->postal_code ?? '' }}</p>
                <p>{{ $profile->address ?? '' }}{{ $profile->building ?? '' }}</p>
                <input type="hidden" name="postal_code" value="{{ $profile->postal_code ?? '' }}">
                <input type="hidden" name="address" value="{{ $profile->address ?? '' }}">
                <input type="hidden" name="building" value="{{ $profile->building ?? '' }}">
                @error('full_address')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- 右：決済情報 --}}
        <div class="payment__info">
            <div class="payment__box">
                <div class="payment__row">
                    <span>商品代金</span>
                    <span>¥{{ number_format($item->price) }}</span>
                </div>

                <div class="payment__row">
                    <span>支払い方法</span>
                    <span id="select-view">未選択</span>
                </div>
            </div>
            <button class="purchase-btn">購入する</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// 選択ボックス表示
document.querySelectorAll('.order__select').forEach(select => {
    const trigger = select.querySelector('.select__trigger');
    const text = select.querySelector('.select__text');
    const options = select.querySelectorAll('.select__options li');
    const view = document.getElementById('select-view');
    const hidden = select.querySelector('input[type="hidden"]');

    trigger.addEventListener('click', () => {
        select.classList.toggle('open');
    });

    options.forEach(option => {
        option.addEventListener('click', (e) => {
            e.stopPropagation();

            text.textContent = option.textContent;
            text.classList.remove('is-placeholder');
            if (view) {
                view.textContent = option.textContent;
            }

            if (hidden) {
                hidden.value = option.dataset.value;
            }

            options.forEach(o => o.classList.remove('selected'));
            option.classList.add('selected');

            select.classList.remove('open');
        });
    });
});
</script>
@endpush