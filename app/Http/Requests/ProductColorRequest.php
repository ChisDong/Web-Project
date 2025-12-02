<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductColorRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'color_name' => 'required|string|max:100',
            'color_code' => 'required|string|max:7',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
