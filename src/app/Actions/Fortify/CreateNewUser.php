<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * ユーザー登録処理
     */
    public function create(array $input): User
    {
        $request = new RegisterRequest();

        // FormRequest利用してバリデーション
        Validator::make($input, $request->rules(), $request->messages())->validate();

        // ユーザー作成
        return User::create([
            'name'     => $input['name'],
            'email'    => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
