@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('page_title')
<h2 class="page__title">商品の出品</h2>
@endsection

@section('content')
<form class="item-form" method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="item-form__section">
        <div class="item-form__group">
            <label>商品画像</label>

            <div class="image-upload">
                <div class="item__image">
                    <img id="preview" src="" style="display: none;">
                </div>
                <label class="image__upload-btn">
                    画像を選択する
                    <input type="file" name="item_image"  id="image" hidden>
                </label>
                <p id="image_error" class="form-error" style="color: red;"></p>
            </div>
            @error('item_image')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="item-form__section">
        <h3 class="item-form__heading">商品の詳細</h3>

        <div class="item-form__group">
            <label>カテゴリー</label>
            <div class="category-group">
                @foreach($categories as $category)
                    <label>
                        <input
                            type="checkbox"
                            name="category_id[]"
                            value="{{ $category->id }}"{{ in_array($category->id, old('category_id', [])) ? 'checked' : '' }}>
                        <span  class="category_input">{{ $category->name }}</span>
                    </label>
                @endforeach
            </div>
            @error('category_id')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="item-form__group">
            <label>商品の状態</label>
            <div class="item__select" data-name="conditions" id="condition-select">
                <input type="hidden" name="condition_id" id="condition_input" value="{{ old('condition_id') }}">
                <div class="select__trigger">
                    <span class="select__text is-placeholder"  id="select-view">選択してください</span>
                    <span class="select__arrow">▼</span>
                </div>
                <ul class="select__options">
                    @foreach($conditions as $condition)
                        <li class="option-item" data-value="{{ $condition->id }}">
                            {{ $condition->name }}
                        </li>
                    @endforeach
                </ul>
                @error('condition_id')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="item-form__section">
        <h3 class="item-form__heading">商品名と説明</h3>

        <div class="item-form__group">
            <label>商品名</label>
            <input type="text" name="name" value="{{ old('name') }}">
            @error('name')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="item-form__group">
            <label>ブランド名</label>
            <input type="text" name="brand" value="{{ old('brand') }}">
        </div>

        <div class="item-form__group">
            <label>商品の説明</label>
            <textarea name="description">{{ old('description') }}</textarea>
            @error('description')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="item-form__group">
            <label>販売価格</label>
            <input type="text" name="price" placeholder="￥" value="{{ old('price') }}">
            @error('price')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>
    </div>
    <button type="submit" class="item-form__submit">出品する</button>
</form>
@endsection

@push('scripts')
<script>
// 画像プレビュー表示
document.addEventListener('DOMContentLoaded', () => {

    const input   = document.getElementById('image');
    const preview = document.getElementById('preview');
    const error   = document.getElementById('image_error');

    if (!input) return;

    input.addEventListener('change', async () => {

        error.textContent = '';

        if (!input.files.length) {
            preview.src = '';
            preview.style.display = 'none';
            return;
        }

        const file = input.files[0];

        // プレビュー事前チェック
        if (!['image/jpeg','image/png'].includes(file.type)) {
            error.textContent = 'プロフィール画像には、jpeg、png形式のファイルを選択してください';
            return;
        }

        const formData = new FormData();
        formData.append('item_image', file);

        try {

            const res = await fetch('{{ route('profile.image.preview') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();

            if (!res.ok) {
                preview.src = '';
                preview.style.display = 'none';
                error.textContent = data.errors?.item_image?.[0] ?? '';
                return;
            }
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';

        } catch {
            error.textContent = '通信エラーが発生しました';
        }
    });
});

// 選択ボックス表示
document.querySelectorAll('.item__select').forEach(select => {
    const trigger = select.querySelector('.select__trigger');
    const text = select.querySelector('.select__text');
    const options = select.querySelectorAll('.select__options li');
    const view = document.getElementById('select-view');
    const hidden = select.querySelector('input[type="hidden"]');

    if (hidden && hidden.value) {
        // hiddenに入っている値(old)と同じdata-valueを持つ項目を探す
        const selectedOption = Array.from(options).find(opt => opt.dataset.value == hidden.value);
        if (selectedOption) {
            text.textContent = selectedOption.textContent;
            text.classList.remove('is-placeholder');
            selectedOption.classList.add('selected');
            if (view) view.textContent = selectedOption.textContent;
        }
    }

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