<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddToCardRequest extends FormRequest
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
            "user_id" => "required|exists:users,id",
            "color_id" => "required|exists:product_colors,id",
            "size_id" => "required|exists:sizes,id",
            "product_id" => "required|exists:products,id",
            "quantity" => "required|integer|min:1",
        ];
    }
}
