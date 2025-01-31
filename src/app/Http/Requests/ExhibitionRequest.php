<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255', // 入力必須
            'brand' => 'nullable|string|max:255', // 未入力可
            'price' => 'required|numeric|min:0', // 入力必須、数値型、0円以上
            'description' => 'required|string|max:255', // 入力必須、最大255文字
            'image' => 'required|image|mimes:jpeg,png|max:2048', // 商品画像のアップロード必須
            'condition' => 'required|integer|exists:conditions,id', // 選択必須
            'categories' => 'required|array', // カテゴリーは配列で受け取る
            'categories.*' => 'exists:categories,id', // 各カテゴリーがDBに存在することを確認
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください。',
            'price.required' => '価格を入力してください。',
            'price.numeric' => '価格は数値で入力してください。',
            'price.min' => '価格は0円以上にしてください。',
            'description.required' => '商品の説明を入力してください。',
            'description.max' => '商品の説明は255文字以内で入力してください。',
            'image.required' => '商品画像を選択してください。',
            'image.mimes' => '画像の形式はJPEGまたはPNGのみ選択してください。',
            'image.max' => '画像のサイズは2MB以下にしてください。',
            'condition.required' => '商品の状態を選択してください。',
            'condition.exists' => '選択された商品の状態は無効です。',
            'categories.required' => 'カテゴリーを選択してください。',
            'categories.*.exists' => '選択されたカテゴリーは無効です。',
        ];
    }
}
