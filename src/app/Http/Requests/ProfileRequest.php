<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    // 郵便番号のチェック
    protected function prepareForValidation()
    {
        if ($this->filled('postal_code')) {
            $digits = preg_replace('/[^0-9]/', '', $this->postal_code);
            if (strlen($digits) === 7) {
                $this->merge([
                    'postal_code' => substr($digits, 0, 3) . '-' . substr($digits, 3),
                ]);
            }
        }
    }
    /**
     * プロフィール編集画面用バリデーションルール
     *
     * @return array
     */
    public function rules()
    {
        if ($this->routeIs('profile.image.preview')) {
            return [
                'profile_image' => ['nullable', 'mimes:jpeg,png'],
            ];
        }

        return [
            'profile_image' => ['sometimes', 'nullable', 'mimes:jpeg,png'],
            'name' => ['required','max:20'],
            'postal_code' => ['required','regex:/^\d{3}-\d{4}$/'],
            'address' => ['required'],
            'building' => ['nullable'],
        ];
    }
    public function messages()
    {
        return [
            'profile_image.mimes' => 'プロフィール画像には、jpeg、png形式のファイルを選択してください',
            'name.required' => 'ユーザ名を入力してください',
            'name.max' => 'ユーザ名は20文字以内で入力してください',
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex'=> '郵便番号の形式が正しくありません',
            'address.required' => '住所を入力してください',
        ];
    }
}
