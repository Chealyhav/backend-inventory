<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_name' => 'required|string|max:255',
            'product_code' => 'required|string|max:255|unique:products,product_code',
            'img_url' => 'nullable|url',
            'sub_category_id' => 'required|exists:sub_category,id',
            'availableStock' => 'nullable|numeric',
            'stockType' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ];
    }
}
