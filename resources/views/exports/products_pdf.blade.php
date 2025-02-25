<!DOCTYPE html>
<html>
<head>
    <title>Products Report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Products Report</h1>
    <table>
        <thead>
            <tr>
                <th>SN</th>
                <th>Product Code</th>
                <th>Image</th>
                <th>Product Name</th>
                <th>Code</th>
                <th>Color</th>
                <th>Package</th>
                <th>Length (mm)</th>
                <th>Buy Price</th>
                <th>Sell Price</th>
                <th>Stock In</th>
                <th>Stock Out</th>
                <th>Current Stock</th>
                <th>Stock Type</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    <td>{{ $row['SN'] }}</td>
                    <td>{{ $row['Product Code'] }}</td>
                    <td>{{ $row['Image'] }}</td>
                    <td>{{ $row['Product Name'] }}</td>
                    <td>{{ $row['Code'] }}</td>
                    <td>{{ $row['Color'] }}</td>
                    <td>{{ $row['Package'] }}</td>
                    <td>{{ $row['Length (mm)'] }}</td>
                    <td>{{ $row['Buy Price'] }}</td>
                    <td>{{ $row['Sell Price'] }}</td>
                    <td>{{ $row['Stock In'] }}</td>
                    <td>{{ $row['Stock Out'] }}</td>
                    <td>{{ $row['Current Stock'] }}</td>
                    <td>{{ $row['Stock Type'] }}</td>
                    <td>{{ $row['Remarks'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
