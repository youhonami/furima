<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'payment_method' => 'required|in:convenience_store,credit_card',
        ];
    }


    public function messages()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください',
        ];
    }
}
