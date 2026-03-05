@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('no-nav', true)

@section('page_title')
<h2 class="page__title">会員登録</h2>
@endsection

@section('content')
<div class="auth">

    {{-- ユーザ登録フォーム --}}
    <form class="auth__form" action="{{ route('register') }}" method="POST" novalidate>
        @csrf
        <div class="auth__group">
            <div class="auth__content">
                <label class="auth__label" for="name">ユーザ名</label>
                <input class="auth__input" id="name" name="name" type="text" value="{{ old('name') }}">
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="auth__group">
            <div class="auth__content">
                <label class="auth__label" for="email">メールアドレス</label>
                <input class="auth__input" id="email" name="email" type="email" value="{{ old('email') }}">
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="auth__group">
            <div class="auth__content">
                <label class="auth__label" for="password">パスワード</label>
                <input class="auth__input" id="password" name="password" type="password">
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="auth__group">
            <div class="auth__content">
                <label class="auth__label" for="password_confirmation">確認用パスワード</label>
                <input class="auth__input" id="password_confirmation" name="password_confirmation" type="password">
                @error('password_confirmation')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button type="submit" class="auth-button">
            登録する
        </button>
    </form>

    <div >
        <a href="{{ route('login') }}" class="auth-link">ログインはこちら</a>
    </div>
</div>
@endsection