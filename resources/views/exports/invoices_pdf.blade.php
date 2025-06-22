<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Invoices Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }

        .header {
            border-bottom: 2px solid #2563eb;
            padding: 20px 0;
            margin-bottom: 20px;
            text-align: center;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 18px;
            color: #666;
            margin-bottom: 5px;
        }

        .report-date {
            font-size: 12px;
            color: #666;
        }

        .summary-section {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8fafc;
            border-radius: 5px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .summary-item {
            text-align: center;
        }

        .summary-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
        }

        .invoices-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 8px;
        }

        .invoices-table th {
            background: #2563eb;
            color: white;
            padding: 8px 4px;
            text-align: left;
            font-size: 8px;
            text-transform: uppercase;
        }

        .invoices-table td {
            padding: 6px 4px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 8px;
        }

        .invoices-table tr:nth-child(even) {
            background: #f8fafc;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 4px;
            border-radius: 8px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending { background: #fef3c7; color: #92400e; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-partially_paid { background: #dbeafe; color: #1e40af; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #666;
            font-size: 8px;
        }

        .page-break {
            page-break-before: always;
        }

        .total-row {
            background: #2563eb !important;
            color: white !important;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">INVENTORY SYSTEM</div>
        <div class="report-title">INVOICES REPORT</div>
        <div class="report-date">Generated on {{ now()->format('M d, Y \a\t g:i A') }}</div>
    </div>

    <div class="summary-section">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Invoices</div>
                <div class="summary-value">{{ $invoices->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Amount</div>
                <div class="summary-value">${{ number_format($invoices->sum('total_amount'), 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Paid Invoices</div>
                <div class="summary-value">{{ $invoices->where('payment_status', 'paid')->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Pending Invoices</div>
                <div class="summary-value">{{ $invoices->where('payment_status', 'pending')->count() }}</div>
            </div>
        </div>
    </div>

    <table class="invoices-table">
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Sub Total</th>
                <th>Discount</th>
                <th>Service Charge</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Payment Method</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->customer_name }}</td>
                    <td>{{ $invoice->formatted_invoice_date }}</td>
                    <td>${{ number_format($invoice->sub_total, 2) }}</td>
                    <td>${{ number_format($invoice->discount, 2) }}</td>
                    <td>${{ number_format($invoice->service_charge, 2) }}</td>
                    <td>${{ number_format($invoice->total_amount, 2) }}</td>
                    <td>
                        <span class="status-badge status-{{ $invoice->payment_status }}">
                            {{ ucfirst(str_replace('_', ' ', $invoice->payment_status)) }}
                        </span>
                    </td>
                    <td>{{ ucfirst($invoice->payment_method) }}</td>
                    <td>{{ $invoice->createdBy ? $invoice->createdBy->name : 'N/A' }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3"><strong>TOTALS</strong></td>
                <td><strong>${{ number_format($invoices->sum('sub_total'), 2) }}</strong></td>
                <td><strong>${{ number_format($invoices->sum('discount'), 2) }}</strong></td>
                <td><strong>${{ number_format($invoices->sum('service_charge'), 2) }}</strong></td>
                <td><strong>${{ number_format($invoices->sum('total_amount'), 2) }}</strong></td>
                <td colspan="3"></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>This report contains {{ $invoices->count() }} invoices with a total value of ${{ number_format($invoices->sum('total_amount'), 2) }}</p>
        <p>Generated by Inventory System on {{ now()->format('M d, Y \a\t g:i A') }}</p>
    </div>
</body>
</html>
