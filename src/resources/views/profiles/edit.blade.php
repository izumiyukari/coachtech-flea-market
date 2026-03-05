@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('page_title')
<h2 class="page__title">プロフィール設定</h2>
@endsection

@section('content')
<div class="mypage__content">
    <form id="profile-form" class="profile__form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="profile__group">
            <div class="profile__content">
                <div class="profile__image-row">
                    <div class="profile__image">
                        <img id="preview" src="{{ $profile && $profile->profile_image ? $profile->image_url : '' }}" style="{{ $profile && $profile->profile_image ? '' : 'display: none;' }}">
                    </div>
                    <div class="profile__button-group">
                        <label class="image__upload-btn">
                            画像を選択する
                            <input type="file" name="profile_image" id="image" hidden>
                        </label>
                        <p id="image_error" class="form-error" style="color: red;"></p>
                        @error('profile_image')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="profile__group">
            <div class="profile__content">
                <label class="profile__label" for="name">ユーザ名</label>
                <input class="profile__input" id="name" name="name" type="text" value="{{ old('name', $user?->name) }}">
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="profile__group">
            <div class="profile__content">
                <label class="profile__label" for="postal_code">郵便番号</label>
                <input class="profile__input" id="postal_code" name="postal_code" type="text" value="{{ old('postal_code', str_replace('-', '', $profile?->postal_code)) }}">
                @error('postal_code')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="profile__group">
            <div class="profile__content">
                <label class="profile__label" for="address">住所</label>
                <input class="profile__input" id="address" name="address" type="text" value="{{ old('address', $profile?->address) }}">
                @error('address')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="profile__group">
            <div class="profile__content">
                <label class="profile__label" for="building">建物名</label>
                <input class="profile__input" id="building" name="building" type="text" value="{{ old('building', $profile?->building) }}">
            </div>
        </div>
        <div >
            <button type="submit" class="update-btn">更新する</button>
        </div>
    </form>
</div>
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
        formData.append('profile_image', file);

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
                error.textContent = data.errors?.profile_image?.[0] ?? '';
                return;
            }
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';

        } catch {
            error.textContent = '通信エラーが発生しました';
        }
    });
});
</script>
@endpush