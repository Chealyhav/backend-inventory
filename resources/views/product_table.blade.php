<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-container {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
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
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Product Details</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
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
                            @foreach($products as $product)
                                <tr>
                                    <td rowspan="{{ count($product['subproducts']) }}">{{ $loop->iteration }}</td>
                                    <td rowspan="{{ count($product['subproducts']) }}">{{ $product['SN'] }}</td>
                                    <td rowspan="{{ count($product['subproducts']) }}" class="text-center">
                                        @if($product['Image'])
                                            <img src="{{ $product['Image'] }}" alt="Product Image" width="50">
                                        @else
                                            <div class="no-image">No Image</div>
                                        @endif
                                    </td>
                                    <td rowspan="{{ count($product['subproducts']) }}">{{ $product['Product Name'] }}</td>
                                    @foreach($product['subproducts'] as $subproduct)
                                        @if (!$loop->first)
                                            <tr>
                                        @endif
                                        <td>{{ $subproduct['Code'] }}</td>
                                        <td>{{ $subproduct['Color'] }}</td>
                                        <td>{{ $subproduct['Package'] }}</td>
                                        <td>{{ $subproduct['Length'] }}</td>
                                        <td>{{ $subproduct['Buy Price'] }}</td>
                                        <td>{{ $subproduct['Sell Price'] }}</td>
                                        <td>{{ $subproduct['Stock In'] }}</td>
                                        <td>{{ $subproduct['Stock Out'] }}</td>
                                        <td><span class="text-danger">{{ $subproduct['Current Stock'] }}</span></td>
                                        <td>{{ $subproduct['Stock Type'] }}</td>
                                        <td>{{ $subproduct['Remarks'] }}</td>
                                        </tr>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
