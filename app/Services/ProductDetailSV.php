<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ProductDetailSV  extends  BaseService
{

    //get all products by category
    public function getAllAluminumProductsByCategory(array $params)
    {
        $categoryId = $params['category'] ?? 2;
        $subcategoryId = $params['subcategory'] ?? null;

        // Start the query to get products based on category
        $query = DB::table('products as p')
            ->leftJoin('subproducts as s', 'p.id', '=', 's.product_id')
            ->leftJoin('sub_category as sc', 'p.sub_category_id', '=', 'sc.id')
            ->leftJoin('categories as c', 'sc.category_id', '=', 'c.id')
            ->leftJoin('colors as col', 's.color_id', '=', 'col.id') // Fixed join condition
            ->where('c.id', '=', $categoryId);

        // If a subcategory ID is provided, filter by subcategory
        if ($subcategoryId !== null) {
            $query->where('p.sub_category_id', '=', $subcategoryId);
        }

        // Apply custom search filter
        if (isset($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('p.product_name', 'LIKE', '%' . $params['search'] . '%')
                    ->orWhere('p.product_code', 'LIKE', '%' . $params['search'] . '%');
            });
        }

        // Apply custom filters if provided
        if (isset($params['filter_by'])) {
            foreach ($params['filter_by'] as $column => $value) {
                $query->where($column, $value);
            }
        }
        if(isset($params['sub_category_id'])) {
            $query->where('p.sub_category_id', $params['sub_category_id']);
         }

        // Pagination setup
        $limit = $params['limit'] ?? 10;
        $page = $params['page'] ?? 1;
        $offset = ($page - 1) * $limit;

        // Apply ordering
        $orderBy = $params['order_by'] ?? 'p.created_at';
        $order = $params['order'] ?? 'asc';
        $query->orderBy($orderBy, $order);

        $total = $query->count();
        $productsData = $query->select(
            'p.id',
            'p.product_code',
            'p.product_name',
            'p.img_url',
            'p.stockType',
            's.code',
            DB::raw('col.name as color_name'),
            'col.id as color_id',
            's.length',
            's.pieces',
            's.thickness',
            's.unit_weight', // Ensure this field is selected
            's.total_weight',
            's.buy_price',
            's.sale_price',
            's.currentStock',
            's.stockIn',
            's.stockOut',
            's.id as subproduct_id',
            DB::raw("CASE WHEN s.remark THEN 'InStock' ELSE 'PreOrder' END as remark")
        )->get();

        // Group the data by product
        $products = [];
        foreach ($productsData as $item) {
            // Group by product id
            if (!isset($products[$item->id])) {
                $products[$item->id] = [
                    'productId' => $item->id,
                    'productCode' => $item->product_code,
                    'productName' => $item->product_name,
                    'productImage' => $item->img_url,
                    'stockType' => $item->stockType,
                    'products' => []
                ];
            }

            // Add subproduct details
            $products[$item->id]['products'][] = [
                'id' => $item->subproduct_id,
                'code' => $item->code,
                'color' => $item->color_name,
                'color_id' => $item->color_id,
                'length' => $item->length,
                'thickness' => $item->thickness,
                'pieces' => $item->pieces,
                'unitWidth' => $item->unit_weight,
                'totalWeight' => $item->total_weight,
                'stockIn' => $item->stockIn,
                'stockOut' => $item->stockOut,
                'currentStock' => $item->currentStock,
                'stockType' => $item->stockType,
                'buyPrice' => $item->buy_price,
                'sellPrice' => $item->sale_price,
                'remarks' => $item->remark,
            ];
        }

        // if not have  subproduct not show  all  have subproduct  can  show
        if (empty($products)) {
            return [];
        }

        // Return the paginated data and the total count
        return array_values($products);
    }

    public function getAccessoriesProductsByCategory(array $params)
    {
        $categoryId = $params['category'] ?? 1;
        $subcategoryId = $params['subcategory'] ?? null;

        // Start the query to get products based on category
        $query = DB::table('products as p')
            ->leftJoin('subproducts as s', 'p.id', '=', 's.product_id')
            ->leftJoin('sub_category as sc', 'p.sub_category_id', '=', 'sc.id')
            ->leftJoin('categories as c', 'sc.category_id', '=', 'c.id')
            ->leftJoin('colors as col', 's.color_id', '=', 'col.id') // Fixed join condition
            ->where('c.id', '=', $categoryId);

        // If a subcategory ID is provided, filter by subcategory
        if ($subcategoryId !== null) {
            $query->where('p.sub_category_id', '=', $subcategoryId);
        }

        // Apply custom search filter
        if (isset($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('p.product_name', 'LIKE', '%' . $params['search'] . '%')
                    ->orWhere('p.product_code', 'LIKE', '%' . $params['search'] . '%');
            });
        }

        if(isset($params['sub_category_id'])) {
           $query->where('p.sub_category_id', $params['sub_category_id']);
        }

        // Apply custom filters if provided
        if (isset($params['filter_by'])) {
            foreach ($params['filter_by'] as $column => $value) {
                $query->where($column, $value);
            }
        }

        // Pagination setup
        $limit = $params['limit'] ?? 10;
        $page = $params['page'] ?? 1;
        $offset = ($page - 1) * $limit;

        // Apply ordering
        $orderBy = $params['order_by'] ?? 'p.created_at';
        $order = $params['order'] ?? 'asc';
        $query->orderBy($orderBy, $order);

        // Count total records for pagination
        $total = $query->count();

        // Execute the query and fetch the product data
        $productsData = $query->select(
            'p.id as product_id',
            'p.id',
            'p.product_code',
            'p.product_name',
            'p.img_url',
            'p.stockType',
            's.code',
            DB::raw('col.name as color_name'),
            'col.id as color_id',
            's.length',
            's.pieces',
            's.buy_price',
            's.sale_price',
            's.currentStock',
            's.stockIn',
            's.stockOut',
            DB::raw("CASE WHEN s.remark THEN 'InStock' ELSE 'PreOrder' END as remark"),
            's.id as subproduct_id',
        )->get();

        // Group the data by product
        $products = [];
        foreach ($productsData as $item) {
            // Group by product id
            if (!isset($products[$item->id])) {
                $products[$item->id] = [
                    'productId' => $item->product_id,
                    'productCode' => $item->product_code,
                    'productName' => $item->product_name,
                    'productImage' => $item->img_url,
                    'stockType' => $item->stockType,
                    'products' => []
                ];
            }

            // Add subproduct details
            $products[$item->id]['products'][] = [
                'id' => $item->subproduct_id,
                'code' => $item->code,
                'color' => $item->color_name,
                'color_id' => $item->color_id,
                'length' => $item->length,
                'pieces' => $item->pieces,
                'stockIn' => $item->stockIn,
                'stockOut' => $item->stockOut,
                'currentStock' => $item->currentStock,
                'stockType' => $item->stockType,
                'buyPrice' => $item->buy_price,
                'sellPrice' => $item->sale_price,
                'remarks' => $item->remark,
            ];
        }

        // Return the paginated data and the total count
        return array_values($products);
    }
}
