<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
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

    /**
     * 商品出品画面用バリデーションルール
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'   => ['required'],
            'description' => ['required', 'max:255'],
            'item_image'  => ['required', 'mimes:jpeg,png'],
            'category_id' => ['required'],
            'condition_id' => ['required'],
            'price'  => ['required', 'integer', 'min:0'],
        ];
    }
    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明は255文字以内で入力してください',
            'item_image.required' => '商品画像をアップロードしてください',
            'item_image.mimes' => '商品画像には、jpeg、png形式のファイルを選択してください',
            'category_id.required' => '商品のカテゴリーを選択してください',
            'condition_id.required' => '商品の状態を選択してください',
            'price.required'  => '商品価格を入力してください',
            'price.integer' => '商品価格は半角数字で入力してください',
            'price.min' => '商品価格には :min 円以上の値を入力してください',
        ];
    }
}
