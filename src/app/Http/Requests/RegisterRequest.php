<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed', // パスワードと確認用パスワードが一致することを確認
            'password_confirmation' => 'required|string|min:8', // 確認用パスワードの入力を必須に
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'お名前を入力してください。r',
            'email.required' => 'メールアドレスを入力してください。r',
            'email.email' => 'メールアドレスの形式が正しくありません。r',
            'password.required' => 'パスワードを入力してください。r',
            'password.min' => 'パスワードは8文字以上で入力してください。r',
            'password.confirmed' => 'パスワードと一致しません。r',
            'password_confirmation.required' => '確認用パスワードを入力してください。r',
            'password_confirmation.min' => '確認用パスワードは8文字以上で入力してください。r',
        ];
    }
}
