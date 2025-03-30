<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\v1\BaseAPI;
use App\Services\CustomerSV;

class CustomerController extends BaseAPI
{
    protected $customer;

    public function __construct(CustomerSV $customer)
    {
        $this->customer = $customer;
    }

    public function index(Request $request)
    {
        try {
            $params = $request->all();
            $data = $this->customer->getAllCustomers($params);
            return $this->successResponse($data, 'Customer list');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $this->customer->customerCreate($request->all());
            return $this->successResponse($data, 'Customer created successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function show($id)
    {
        try {
            $data = $this->customer->getCustomerById($id);
            return $this->successResponse($data, 'Customer details');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $this->customer->customerUpdate($id, $request->all());
            return $this->successResponse($data, 'Customer updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function destroy($id)
    {
        try {
            $data = $this->customer->customerDelete($id);
            return $this->successResponse($data, 'Customer deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
}