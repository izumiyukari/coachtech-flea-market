<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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

    // 郵便番号、住所、マンション名を結合
    protected function prepareForValidation()
    {
        $this->merge([
            'full_address' => $this->postal_code . $this->address . $this->building,
        ]);
    }
    /**
     * 商品購入画面用バリデーションルール
     *
     * @return array
     */
    public function rules()
    {
        return [
            'full_address' => ['required'],
            'payment_method' => ['required'],
            'postal_code'    => ['nullable'],
            'address'        => ['nullable'],
            'building'       => ['nullable'],
        ];
    }
    public function messages()
    {
        return [
            'full_address.required' => '配送先を選択してください',
            'payment_method.required' => '支払い方法を選択してください',
        ];
    }
}
