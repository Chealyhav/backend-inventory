<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $rules = [
            'customer_id' => 'nullable|exists:customers,id',
            'updated_price' => 'nullable|numeric|min:0',
            'sale_id' => 'required|exists:sales,id',
            'total_price' => 'required|numeric|min:0',
            'sub_total' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'service_charge' => 'nullable|numeric|min:0',
            'service_name' => 'nullable|string|max:255',
            'payment_method' => 'nullable|string|in:cash,card,bank_transfer,check',
            'payment_status' => 'nullable|string|in:pending,paid,partially_paid,cancelled',
            'total_amount' => 'required|numeric|min:0',
            'payment_id' => 'nullable|exists:payments,id',
            'notes' => 'nullable|string|max:1000',
            'invoice_date' => 'nullable|date',
            'status' => 'nullable|boolean',
        ];

        // Add unique validation for invoice_number on create
        if ($this->isMethod('POST')) {
            $rules['invoice_number'] = 'nullable|string|unique:invoices,invoice_number';
        }

        // Add unique validation for invoice_number on update (excluding current record)
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['invoice_number'] = 'nullable|string|unique:invoices,invoice_number,' . $this->route('id');
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'customer_id.exists' => 'The selected customer does not exist.',
            'sale_id.required' => 'The sale ID is required.',
            'sale_id.exists' => 'The selected sale does not exist.',
            'total_price.required' => 'The total price is required.',
            'total_price.numeric' => 'The total price must be a number.',
            'total_price.min' => 'The total price must be at least 0.',
            'total_amount.required' => 'The total amount is required.',
            'total_amount.numeric' => 'The total amount must be a number.',
            'total_amount.min' => 'The total amount must be at least 0.',
            'payment_id.exists' => 'The selected payment does not exist.',
            'payment_method.in' => 'The payment method must be cash, card, bank_transfer, or check.',
            'payment_status.in' => 'The payment status must be pending, paid, partially_paid, or cancelled.',
            'invoice_number.unique' => 'This invoice number already exists.',
            'invoice_date.date' => 'The invoice date must be a valid date.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'customer_id' => 'customer',
            'sale_id' => 'sale',
            'total_price' => 'total price',
            'sub_total' => 'sub total',
            'service_charge' => 'service charge',
            'service_name' => 'service name',
            'payment_method' => 'payment method',
            'payment_status' => 'payment status',
            'total_amount' => 'total amount',
            'payment_id' => 'payment',
            'invoice_date' => 'invoice date',
            'invoice_number' => 'invoice number',
        ];
    }
}
