@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('no-nav', true)

@section('no-page_title', true)

@section('content')
<div class="auth">
    <div class="auth__form">
        <p class="verify__text">
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            メール認証を完了してください。
        </p>

        {{-- メール認証：Mailtrapへ --}}
        <div class="auth-link-box">
            <a href="https://mailtrap.io"  class="verify-button">
                認証はこちらから
            </a>
        </div>

        <form method="POST" action="{{ route('verification.send') }}"  class="auth-resend-form">
            @csrf
            <button type="submit" class="resend-link-button">
                認証メールを再送する
            </button>
        </form>
    </div>
</div>
@endsection
