<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>CoachtechFleaMarket</title>
        <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
        <link rel="stylesheet" href="{{ asset('css/common.css') }}">
        @yield('css')
    </head>
    <body>

        {{-- ヘッダー表示 --}}
        <header class="header">
            <div class="header_content">
                {{-- ロゴ --}}
                <h1 class="header__logo">
                    <a href="{{ route('items.index') }}">
                        <img src="{{ asset('images/common/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH" class="logo">
                    </a>
                </h1>

                @unless(View::hasSection('no-nav'))
                    {{-- 検索 --}}
                    <form action="{{ url()->current() }}" method="GET" class="header__search">
                        {{-- 商品一覧用 --}}
                        @if(request()->routeIs('items.index'))
                            <input type="hidden" name="tab" value="{{ request('tab', 'recommend') }}">
                        @endif

                        {{-- マイページ用 --}}
                        @if(request()->routeIs('profile.index'))
                            <input type="hidden" name="page" value="{{ request('page', 'sell') }}">
                        @endif

                        <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="なにをお探しですか？">
                    </form>

                    {{-- ナビ --}}
                    <nav class="header__nav">
                        @auth
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="header__link" type="submit">ログアウト</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="header__link">ログイン</a>
                        @endauth
                        <a href="{{ route('profile.index') }}" class="header__link">マイページ</a>
                        <a href="{{ route('items.create') }}" class="header__button">出品</a>
                    </nav>
                @endunless
            </div>
        </header>

        <main>
            @hasSection('page_title')
                <div class="main--narrow">
                    @yield('page_title')
                    @yield('content')
                </div>
            @else
                <div class="main--full">
                    @yield('content')
                </div>
            @endif
        </main>
        @stack('scripts')
    </body>
</html>