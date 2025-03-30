<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Services\CloudinarySV;

class CustomerSV extends BaseService
{
    protected $cloudinarySv;

    public function __construct()
    {
        $this->cloudinarySv = new CloudinarySV();
    }

    public function getQuery()
    {
        return Customer::query();
    }

    public function getAllCustomers($params = [])
    {
        $query = DB::table('customers as c')
            ->select(
                'c.id',
                'c.name',
                'c.company_name',
                'c.email',
                'c.phone_number',
                'c.address',
                'c.created_at',
                'c.updated_at',
                'c.deleted_at',
                'c.status'
            );

        // Search functionality
        if (!empty($params['search'])) {
            $searchTerm = '%' . $params['search'] . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('c.name', 'LIKE', $searchTerm)
                    ->orWhere('c.company_name', 'LIKE', $searchTerm)
                    ->orWhere('c.email', 'LIKE', $searchTerm)
                    ->orWhere('c.phone_number', 'LIKE', $searchTerm);
            });
        }

        // Apply filters
        if (!empty($params['filter_by']) && is_array($params['filter_by'])) {
            foreach ($params['filter_by'] as $column => $value) {
                $query->where("c.$column", $value);
            }
        }

        // Status filter
        if (!empty($params['status'])) {
            $query->where('c.status', $params['status']);
        }

        // Sorting
        if (!empty($params['order_by'])) {
            $query->orderBy($params['order_by'], $params['order_direction'] ?? 'asc');
        }

        // Get total count before pagination
        $total = $query->count();

        // Pagination
        $limit = $params['limit'] ?? 10;
        $page = $params['page'] ?? 1;
        $offset = ($page - 1) * $limit;
        $totalPage = ceil($total / $limit);
        $nextPage = $page < $totalPage ? $page + 1 : 0;
        $prevPage = $page > 1 ? $page - 1 : 0;

        $customers = $query->offset($offset)->limit($limit)->get();

        return [
            'total' => $total,
            'totalPage' => $totalPage,
            'nextPage' => $nextPage,
            'prevPage' => $prevPage,
            'currentPage' => $page,
            'limit' => $limit,
            'data' => $customers,
        ];
    }

    public function getCustomerById($id)
    {
        return Customer::find($id);
    }

    public function customerCreate(array $params = [])
    {
        $query = $this->getQuery();
        if (empty($params['name'])) {
            throw new \InvalidArgumentException('Name is required.');
        }
        if (empty($params['company_name'])) {
            throw new \InvalidArgumentException('Company name is required.');
        
        }
        if (empty($params['phone_number'])) {
            throw new \InvalidArgumentException('Phone number is required.');
        }
        $customer = $query->create([
            'name' => data_get($params, 'name'),
            'company_name' => data_get($params, 'company_name'),
            'email' => data_get($params, 'email'),
            'phone_number' => data_get($params, 'phone_number'),
            'address' => data_get($params, 'address'),
            'status' => data_get($params, 'status', true),
        ]);

        return $customer;
    }

    public function customerUpdate($id, array $params = [])
    {
        $query = $this->getQuery();
        $customer = $query->find($id);
        if (!$customer) {
            throw new ModelNotFoundException('Customer not found.');
        }

        $customer->update($params);
        return $customer;
    }

    public function customerDelete($id)
    {
        $customer = $this->getCustomerById($id);
        if (!$customer) {
            throw new ModelNotFoundException('Customer not found.');
        }

        $customer->delete();
        return $customer;
    }
}