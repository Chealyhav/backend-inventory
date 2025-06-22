<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'updated_price',
        'sale_id',
        'total_price',
        'sub_total',
        'discount',
        'service_charge',
        'service_name',
        'payment_method',
        'payment_status',
        'total_amount',
        'payment_id',
        'notes',
        'invoice_date',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'total_price' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'discount' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'updated_price' => 'decimal:2',
        'status' => 'boolean',
    ];

    /**
     * Get the customer that owns the invoice
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the sale that owns the invoice
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the payment that owns the invoice
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get the user who created the invoice
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the invoice
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who deleted the invoice
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Get customer name attribute
     */
    public function getCustomerNameAttribute(): string
    {
        return $this->customer ? $this->customer->name : 'N/A';
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAmountAttribute(): string
    {
        return '$' . number_format($this->total_amount, 2);
    }

    /**
     * Get formatted invoice date
     */
    public function getFormattedInvoiceDateAttribute(): string
    {
        return $this->invoice_date ? $this->invoice_date->format('M d, Y') : 'N/A';
    }

    /**
     * Get payment status badge
     */
    public function getPaymentStatusBadgeAttribute(): string
    {
        $statuses = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'paid' => 'bg-green-100 text-green-800',
            'partially_paid' => 'bg-blue-100 text-blue-800',
            'cancelled' => 'bg-red-100 text-red-800'
        ];

        $status = $this->payment_status ?? 'pending';
        $classes = $statuses[$status] ?? 'bg-gray-100 text-gray-800';

        return '<span class="px-2 py-1 text-xs font-medium rounded-full ' . $classes . '">' .
               ucfirst(str_replace('_', ' ', $status)) . '</span>';
    }

    /**
     * Scope for active invoices
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for paid invoices
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * Scope for pending invoices
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }
}
