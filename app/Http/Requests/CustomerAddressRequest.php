<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerAddressRequest extends FormRequest
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
    public function rules(): array
    {
        $createRules = [
            'customer_id' => 'required|integer',
            'address_line' => 'nullable|string|max:255',
            'ward' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
        ];

        $updateRules = [
            'address_line' => 'sometimes|nullable|string|max:255',
            'ward' => 'sometimes|string|max:100',
            'district' => 'sometimes|string|max:100',
            'city' => 'sometimes|string|max:100',
            'country' => 'sometimes|string|max:100',
        ];
        return $this->isMethod('put') ? $updateRules : $createRules;
    }
}
