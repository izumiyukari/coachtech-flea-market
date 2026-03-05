<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 会員登録成功後の遷移
        $this->app->singleton(RegisterResponseContract::class, fn() => new class implements RegisterResponseContract {
            public function toResponse($request) {
                return redirect()->route('verification.notice');
            }
        });

        // ログイン後の遷移
        $this->app->singleton(LoginResponseContract::class, fn() => new class implements LoginResponseContract {
            public function toResponse($request) {

                // ユーザーがログイン済みで、かつメール認証が済んでいない場合
                if ($request->user() && !$request->user()->hasVerifiedEmail()) {
                    return redirect()->route('verification.notice');
                }
                // 認証済みなら本来のトップ画面へ
                return redirect()->intended(route('items.index'));
            }
        });

        // ログアウト後の遷移
        $this->app->singleton(LogoutResponseContract::class, fn() => new class implements LogoutResponseContract {
            public function toResponse($request) {
                return redirect('/');
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ログイン制限の設定（429エラー対策）
        RateLimiter::for('login', function (Request $request) {
            // 開発中のため制限なし
            return Limit::none()->by($request->email.$request->ip());
        });

        // ログイン画面にどのビューを使うかを指定
        Fortify::loginView(fn() => view('auth.login'));

        // 登録画面の場合
        Fortify::registerView(fn() => view('auth.register'));

        // アクションの登録
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        Fortify::authenticateUsing(function (Request $request)
        {
            // LoginRequestのバリデーションを実行
            $loginRequest = new LoginRequest();
            $request->validate($loginRequest->rules(), $loginRequest->messages());

            $user = User::where('email', $request->email)->first();

            // ログイン成功時:ユーザーを返す
            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        });
    }
}
