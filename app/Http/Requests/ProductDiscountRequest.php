<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductDiscountRequest extends FormRequest
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
            'code' => 'required|string|max:50|unique:product_discounts,code,' . $this->route('product_discount'),
            'percent' => 'required|numeric|min:0|max:100',
            'max_value' => 'nullable|numeric|min:0',
            'expiry_at' => 'nullable|date|after:today',
        ];
    }
}
