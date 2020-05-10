<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'name' => 'required|string',
            'description' => 'required|string',
            'price'=> 'required|numeric',
            'weight' => 'required|numeric',
            'categories' => 'required|string',
            'stock' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png'
        ];
    }
}
