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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'], // ハイフンありの8文字
            'address' => 'required',
            'building' => 'required',
            'profile_image' => 'nullable|mimes:jpeg,png|max:2048',

        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'お名前を入力してください。',
            'postal_code.required' => '郵便番号を入力してください。',
            'postal_code.regex' => '郵便番号はハイフンを含む8文字（例: 123-4567）で入力してください。',
            'address.required' => '住所を入力してください。',
            'building.required' => '建物名を入力してください。',
            'img.mimes' => '画像の形式はJPEGまたはPNGのみ選択してください。',
            'profile_image.max' => '画像のサイズは2MB以下にしてください。',
        ];
    }
}
