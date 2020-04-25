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
            'name' => 'required|string|min:6|max:32',
            'email' => 'required|email|min:8|max:32',
            'password' => 'required|string|min:6|max:32',
            'password_confirmation' => 'required|same:password',
            'billing_address' => 'required|string',
            'shipping_address' => 'required|string',
            'birthdate' => 'nullable|date',
            'phone' => 'required|string'
        ];
    }
}
