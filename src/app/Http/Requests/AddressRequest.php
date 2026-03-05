<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
     * 住所変更画面用バリデーションルール
     *
     * @return array
     */
    public function rules()
    {
        return [
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address'  => ['required'],
            'building'    => ['nullable'],
        ];
    }
    public function messages()
    {
        return [
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex'=> '郵便番号の形式が正しくありません',
            'address.required' => '住所を入力してください',
        ];
    }
}
