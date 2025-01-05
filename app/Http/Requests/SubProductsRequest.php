<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubProductsRequest extends FormRequest
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

            'product_id' => 'required|exists:products,id',
            'color_id' => 'required|exists:colors,id',
            'code' => 'required|string|max:255|unique:subproducts,code,',
            'pieces' => 'nullable|integer',
            'thickness' => 'nullable|numeric',
            'length' => 'required|numeric',
            'unit_weight' => 'nullable|numeric',
            'total_weight' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'buy_price' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'status' => 'required|boolean',
        ];
    }
}
