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
        }
        .header {
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 12px;
        }
        th {
            background-color: #f4f4f4;
            color: #333;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
        }
        td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Generated on: {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nº</th>
                <th>SN</th>
                <th>Image</th>
                <th>Product Name</th>
                <th>Code</th>
                <th>Color</th>
                <th>Package</th>
                <th>Length (mm)</th>
                <th>Thickness (mm)</th>
                <th>Weight per Unit (kg)</th>
                <th>Total Weight (kg)</th>
                <th>Buy Price</th>
                <th>Sell Price</th>
                <th>In</th>
                <th>Out</th>
                <th>Stock</th>
                <th>Type</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['SN'] }}</td>
                    <td>{{ $row['Image'] }}</td>
                    <td>{{ $row['Product Name'] }}</td>
                    <td>{{ $row['Code'] }}</td>
                    <td>{{ $row['Color'] }}</td>
                    <td class="text-right">{{ $row['Package'] }}</td>
                    <td class="text-right">{{ $row['Length (mm)'] }}</td>
                    <td class="text-right">{{ $row['Thickness (mm)'] }}</td>
                    <td class="text-right">{{ $row['Weight per Unit (kg)'] }}</td>
                    <td class="text-right">{{ $row['Total Weight (kg)'] }}</td>
                    <td class="text-right">{{ $row['Buy Price'] }}</td>
                    <td class="text-right">{{ $row['Sell Price'] }}</td>
                    <td class="text-right">{{ $row['Stock In'] }}</td>
                    <td class="text-right">{{ $row['Stock Out'] }}</td>
                    <td class="text-right @if($row['Stock'] > 0) stock-positive @elseif($row['Stock'] == 0) stock-zero @else stock-negative @endif">
                        {{ $row['Stock'] }}
                    </td>
                    <td>{{ $row['Type'] }}</td>
                    <td>{{ $row['Remarks'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
