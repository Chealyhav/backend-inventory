<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\v1\BaseAPI;
use App\Services\ProductDetailSV;

class ProductDetailController extends BaseAPI
{
    protected $productDetail;

    public function __construct()
    {
        $this->productDetail = new ProductDetailSV();
    }


    public function getAluminum(Request $request)
    {
        try {
            $params = $request->all();
            $productDetails = $this->productDetail->getAllAluminumProductsByCategory($params);
            return $this->sendResponse($productDetails, 'Product details retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(),$e->getCode());
        }
    }

    public function getAccessories(Request $request)
    {
        try {
            $params = $request->all();
            $productDetails = $this->productDetail->getAccessoriesProductsByCategory($params);
            return $this->sendResponse($productDetails, 'Product details retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(),$e->getCode());
        }
    }
}
