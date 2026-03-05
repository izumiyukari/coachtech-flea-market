@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('no-nav', true)

@section('page_title')
<h2 class="page__title">ログイン</h2>
@endsection

@section('content')
<div class="auth">
    {{-- ログインフォーム --}}
    <form class="auth__form" action="{{ route('login') }}" method="POST" novalidate>
        @csrf
        @error('login')
            <p class="auth__form--login">{{ $message }}</p>
        @enderror
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

        <button type="submit" class="auth-button">
            ログインする
        </button>
    </form>

    <div >
        <a href="{{ route('register') }}" class="auth-link">会員登録はこちら</a>
    </div>
</div>
@endsection