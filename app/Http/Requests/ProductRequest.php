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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'collection_id' => 'required|exists:collections,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $this->route('product'),
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|lt:base_price',
            'gender' => 'required|in:male,female,unisex',
            'status' => 'required|in:active,deactive',
        ];
    }
}
