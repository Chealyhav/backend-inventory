<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .header {
            margin-bottom: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
            background-color: #fff;
        }
        th {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }
        td {
            padding: 10px;
            border: 1px solid #ddd;
            vertical-align: top;
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .stock-positive {
            color: #28a745;
        }
        .stock-negative {
            color: #dc3545;
        }
        .stock-zero {
            color: #ffc107;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .no-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tbody tr:hover {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Generated on: {{ $date }}</p>

        {{-- <div> {{ $data }} </div> --}}

    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">#</th>
                <th rowspan="2">SN</th>
                <th rowspan="2">Image</th>
                <th rowspan="2">Product Name</th>
                <th rowspan="2">Code</th>
                <th rowspan="2">Color</th>
                <th rowspan="2">Package</th>
                <th rowspan="2">Length (mm)</th>
                <th colspan="2">Price</th>
                <th colspan="4">Stock</th>
                <th rowspan="2">Remarks</th>
            </tr>
            <tr>
                <th>Buy Price</th>
                <th>Sell Price</th>
                <th>Stock In</th>
                <th>Stock Out</th>
                <th>Current Stock</th>
                <th>Stock Type</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
                @foreach($row['products'] as $subIndex => $product)
                    <tr>
                        @if($subIndex === 0)
                            <td rowspan="{{ count($row['products']) }}">{{ $index + 1 }}</td>
                            <td rowspan="{{ count($row['products']) }}">{{ $row['productCode'] }}</td>
                            <td rowspan="{{ count($row['products']) }}">
                                @if(isset($row['productImage']) && $row['productImage'])
                                    <img src="{{ $row['productImage'] }}" alt="Product Image" width="50">
                                @else
                                    <div class="no-image">No Image</div>
                                @endif
                            </td>
                            <td rowspan="{{ count($row['products']) }}">{{ $row['productName'] }}</td>
                        @endif
                        <td>{{ $product['code'] }}</td>
                        <td>{{ $product['color'] }}</td>
                        <td class="text-right">{{ $product['pieces'] }}</td>
                        <td class="text-right">{{ $product['length'] }}</td>
                        <td class="text-right">{{ $product['buyPrice'] }}</td>
                        <td class="text-right">{{ $product['sellPrice'] }}</td>
                        <td class="text-right">{{ $product['stockIn'] }}</td>
                        <td class="text-right">{{ $product['stockOut'] }}</td>
                        <td class="text-right @if($product['currentStock'] > 0) stock-positive @elseif($product['currentStock'] == 0) stock-zero @else stock-negative @endif">
                            {{ $product['currentStock'] }}
                        </td>
                        <td>{{ $product['stockType'] }}</td>
                        <td>{{ $product['remarks'] }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>End of Report</p>
    </div>
</body>
</html>
@foreach($data as $index => $product)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $product['Product Code'] }}</td>
        <td>
            @if($product['Image'] !== 'No Image')
                <img src="{{ $product['Image'] }}" alt="Product Image" width="50">
            @else
                <div class="no-image">No Image</div>
            @endif
        </td>
        <td>{{ $product['Product Name'] }}</td>
        <td>{{ $product['Code'] }}</td>
        <td>{{ $product['Color'] }}</td>
        <td class="text-right">{{ $product['Package'] }}</td>
        <td class="text-right">{{ $product['Length (mm)'] }}</td>
        <td class="text-right">{{ $product['Buy Price'] }}</td>
        <td class="text-right">{{ $product['Sell Price'] }}</td>
        <td class="text-right">{{ $product['Stock In'] }}</td>
        <td class="text-right">{{ $product['Stock Out'] }}</td>
        <td class="text-right @if($product['Stock'] > 0) stock-positive @elseif($product['Stock'] == 0) stock-zero @else stock-negative @endif">
            {{ $product['Stock'] }}
        </td>
        <td>{{ $product['Type'] }}</td>
        <td>{{ $product['Remarks'] }}</td>
    </tr>
@endforeach
