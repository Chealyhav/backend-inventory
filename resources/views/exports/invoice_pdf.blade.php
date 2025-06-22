<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }

        .header {
            border-bottom: 2px solid #2563eb;
            padding: 20px 0;
            margin-bottom: 30px;
        }

        .company-info {
            float: left;
            width: 50%;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }

        .company-details {
            color: #666;
            line-height: 1.6;
        }

        .invoice-info {
            float: right;
            width: 40%;
            text-align: right;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }

        .invoice-number {
            font-size: 16px;
            color: #666;
            margin-bottom: 5px;
        }

        .invoice-date {
            color: #666;
        }

        .clear {
            clear: both;
        }

        .customer-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }

        .customer-info {
            display: inline-block;
            width: 48%;
            vertical-align: top;
        }

        .customer-details {
            background: #f8fafc;
            padding: 15px;
            border-radius: 5px;
        }

        .customer-name {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .customer-email, .customer-phone {
            color: #666;
            margin-bottom: 3px;
        }

        .invoice-details {
            display: inline-block;
            width: 48%;
            vertical-align: top;
            margin-left: 4%;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .detail-item {
            background: #f8fafc;
            padding: 10px;
            border-radius: 5px;
        }

        .detail-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .detail-value {
            font-weight: bold;
            font-size: 12px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .items-table th {
            background: #2563eb;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }

        .items-table tr:nth-child(even) {
            background: #f8fafc;
        }

        .total-section {
            margin-top: 30px;
            text-align: right;
        }

        .total-row {
            margin-bottom: 8px;
            font-size: 12px;
        }

        .total-label {
            display: inline-block;
            width: 120px;
            text-align: right;
            margin-right: 10px;
        }

        .total-value {
            display: inline-block;
            width: 100px;
            text-align: right;
            font-weight: bold;
        }

        .grand-total {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
            border-top: 2px solid #2563eb;
            padding-top: 10px;
            margin-top: 10px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #666;
            font-size: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending { background: #fef3c7; color: #92400e; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-partially_paid { background: #dbeafe; color: #1e40af; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }

        .notes-section {
            margin-top: 30px;
            padding: 15px;
            background: #f8fafc;
            border-radius: 5px;
        }

        .notes-title {
            font-weight: bold;
            margin-bottom: 8px;
            color: #2563eb;
        }

        .notes-content {
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <div class="company-name">INVENTORY SYSTEM</div>
            <div class="company-details">
                123 Business Street<br>
                City, State 12345<br>
                Phone: (555) 123-4567<br>
                Email: info@inventory.com
            </div>
        </div>
        <div class="invoice-info">
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-number">#{{ $invoice->invoice_number }}</div>
            <div class="invoice-date">{{ $invoice->formatted_invoice_date }}</div>
        </div>
        <div class="clear"></div>
    </div>

    <div class="customer-section">
        <div class="section-title">BILL TO</div>
        <div class="customer-info">
            <div class="customer-details">
                <div class="customer-name">{{ $invoice->customer_name }}</div>
                @if($invoice->customer)
                    <div class="customer-email">{{ $invoice->customer->email }}</div>
                    <div class="customer-phone">{{ $invoice->customer->phone }}</div>
                    @if($invoice->customer->address)
                        <div class="customer-address">{{ $invoice->customer->address }}</div>
                    @endif
                @else
                    <div class="customer-email">N/A</div>
                    <div class="customer-phone">N/A</div>
                @endif
            </div>
        </div>
        <div class="invoice-details">
            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-label">Payment Status</div>
                    <div class="detail-value">
                        <span class="status-badge status-{{ $invoice->payment_status }}">
                            {{ ucfirst(str_replace('_', ' ', $invoice->payment_status)) }}
                        </span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Payment Method</div>
                    <div class="detail-value">{{ ucfirst($invoice->payment_method) }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Invoice Date</div>
                    <div class="detail-value">{{ $invoice->formatted_invoice_date }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Created By</div>
                    <div class="detail-value">{{ $invoice->createdBy ? $invoice->createdBy->name : 'N/A' }}</div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>

    @if($invoice->sale && $invoice->sale->saleItems)
        <div class="section-title">INVOICE ITEMS</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->sale->saleItems as $item)
                    <tr>
                        <td>{{ $item->product ? $item->product->name : 'N/A' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>${{ number_format($item->unit_price, 2) }}</td>
                        <td>${{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="total-section">
        <div class="total-row">
            <span class="total-label">Sub Total:</span>
            <span class="total-value">${{ number_format($invoice->sub_total, 2) }}</span>
        </div>
        @if($invoice->discount > 0)
            <div class="total-row">
                <span class="total-label">Discount:</span>
                <span class="total-value">-${{ number_format($invoice->discount, 2) }}</span>
            </div>
        @endif
        @if($invoice->service_charge > 0)
            <div class="total-row">
                <span class="total-label">Service Charge:</span>
                <span class="total-value">${{ number_format($invoice->service_charge, 2) }}</span>
            </div>
        @endif
        <div class="total-row grand-total">
            <span class="total-label">Total Amount:</span>
            <span class="total-value">${{ number_format($invoice->total_amount, 2) }}</span>
        </div>
    </div>

    @if($invoice->notes)
        <div class="notes-section">
            <div class="notes-title">Notes:</div>
            <div class="notes-content">{{ $invoice->notes }}</div>
        </div>
    @endif

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>This is a computer generated invoice. No signature required.</p>
        <p>Generated on {{ now()->format('M d, Y \a\t g:i A') }}</p>
    </div>
</body>
</html>
