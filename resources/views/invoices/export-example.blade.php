<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Export Examples - Inventory System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Invoice Export Examples</h1>
                        <p class="mt-1 text-sm text-gray-500">Demonstration of invoice export functionality</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('invoices.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                            <i class="fas fa-arrow-left"></i>
                            <span>Back to Invoices</span>
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Export Options Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Excel Export -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center mb-4">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-file-excel text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Export to Excel</h3>
                            <p class="text-sm text-gray-500">Download all invoices as Excel file</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <a href="{{ route('invoices.export.excel') }}" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg flex items-center justify-center space-x-2">
                            <i class="fas fa-download"></i>
                            <span>Export All Invoices to Excel</span>
                        </a>
                        <div class="text-xs text-gray-500">
                            <p>• Includes all invoice data</p>
                            <p>• Formatted for easy reading</p>
                            <p>• Compatible with Microsoft Excel</p>
                        </div>
                    </div>
                </div>

                <!-- PDF Export All -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center mb-4">
                        <div class="p-3 rounded-full bg-red-100 text-red-600">
                            <i class="fas fa-file-pdf text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Export All to PDF</h3>
                            <p class="text-sm text-gray-500">Download all invoices as PDF report</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <a href="{{ route('invoices.export.all-pdf') }}" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg flex items-center justify-center space-x-2">
                            <i class="fas fa-download"></i>
                            <span>Export All Invoices to PDF</span>
                        </a>
                        <div class="text-xs text-gray-500">
                            <p>• Complete invoice report</p>
                            <p>• Professional formatting</p>
                            <p>• Ready for printing</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sample Invoice Data -->
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Sample Invoice Data (Mock)</h3>
                    <p class="text-sm text-gray-500">This is the data that will be exported</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach([1, 2, 3, 4, 5] as $id)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">INV202501{{ str_pad($id, 3, '0', STR_PAD_LEFT) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @php
                                                $customers = ['John Doe', 'Jane Smith', 'Bob Johnson', 'Alice Brown', 'Charlie Wilson'];
                                            @endphp
                                            {{ $customers[$id - 1] }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            @php
                                                $emails = ['john@example.com', 'jane@example.com', 'bob@example.com', 'alice@example.com', 'charlie@example.com'];
                                            @endphp
                                            {{ $emails[$id - 1] }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @php
                                            $dates = ['2025-01-15', '2025-01-16', '2025-01-17', '2025-01-18', '2025-01-19'];
                                        @endphp
                                        {{ $dates[$id - 1] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            @php
                                                $amounts = [1500.00, 2300.50, 890.75, 3200.00, 1750.25];
                                            @endphp
                                            ${{ number_format($amounts[$id - 1], 2) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statuses = ['paid', 'pending', 'paid', 'partially_paid', 'pending'];
                                            $statusClasses = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'paid' => 'bg-green-100 text-green-800',
                                                'partially_paid' => 'bg-blue-100 text-blue-800'
                                            ];
                                            $status = $statuses[$id - 1];
                                            $statusClass = $statusClasses[$status];
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('invoices.export.pdf', $id) }}" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-file-pdf mr-1"></i>Export PDF
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Individual Invoice Export Examples -->
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Individual Invoice Export</h3>
                    <p class="text-sm text-gray-500">Export specific invoices as PDF</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach([1, 2, 3] as $id)
                            <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-medium text-gray-900">INV202501{{ str_pad($id, 3, '0', STR_PAD_LEFT) }}</h4>
                                        <p class="text-sm text-gray-500">
                                            @php
                                                $customers = ['John Doe', 'Jane Smith', 'Bob Johnson'];
                                            @endphp
                                            {{ $customers[$id - 1] }}
                                        </p>
                                    </div>
                                    <span class="text-sm text-gray-500">
                                        @php
                                            $dates = ['2025-01-15', '2025-01-16', '2025-01-17'];
                                        @endphp
                                        {{ $dates[$id - 1] }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center mb-3">
                                    <span class="font-medium text-gray-900">
                                        @php
                                            $amounts = [1500.00, 2300.50, 890.75];
                                        @endphp
                                        ${{ number_format($amounts[$id - 1], 2) }}
                                    </span>
                                    @php
                                        $statuses = ['paid', 'pending', 'paid'];
                                        $statusClasses = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'paid' => 'bg-green-100 text-green-800'
                                        ];
                                        $status = $statuses[$id - 1];
                                        $statusClass = $statusClasses[$status];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </div>
                                <a href="{{ route('invoices.export.pdf', $id) }}" class="w-full bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md flex items-center justify-center space-x-2 text-sm">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>Export PDF</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Export Features -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Export Features</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-3">Excel Export Features</h4>
                            <ul class="space-y-2 text-sm text-gray-600">
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    All invoice data included
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Customer information
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Payment details
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Formatted for analysis
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Compatible with Excel/Google Sheets
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 mb-3">PDF Export Features</h4>
                            <ul class="space-y-2 text-sm text-gray-600">
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Professional formatting
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Company branding ready
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Print-friendly layout
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Individual or bulk export
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Secure document format
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading states to export buttons
            const exportButtons = document.querySelectorAll('a[href*="export"]');
            exportButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exporting...';
                    this.style.pointerEvents = 'none';
                    
                    // Reset after 3 seconds (in case of error)
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.style.pointerEvents = 'auto';
                    }, 3000);
                });
            });
        });
    </script>
</body>
</html> 