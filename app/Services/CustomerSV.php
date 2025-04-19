<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class CustomerSV extends BaseService
{
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
                'c.gender',
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
                    ->orWhere('c.company_name', 'LIKE', $searchTerm);
            });
        }

        //Status filter
        if (!empty($params['status'])) {
            $query->where('c.status', $params['status']);
        }

        // Sorting
        if (!empty($params['order_by'])) {
            $query->orderBy($params['order_by'], $params['c.created_a'] ?? 'asc');
        }

        // Pagination count
        $total = $query->count();

        // Pagination
        $limit = $params['limit'] ?? 10;
        $page = $params['page'] ?? 1;
        $offset = ($page - 1) * $limit;
        $totalPage = ceil($total / $limit);
        $nextPage = $page < $totalPage ? $page + 1 : 0;
        $prevPage = $page > 1 ? $page - 1 : 0;

        $data = $query->offset($offset)->limit($limit)->get();

        $customers = $query->get();

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

    public function customerCreate(array $params = [])
    {
        $query = $this->getQuery();

        if (!isset($params['name'], $params['company_name'], $params['phone_number'])) {
            throw new \InvalidArgumentException('Name, Company name, and Phone number are required.');
        }

        $data = $query->create([
            'name' => $params['name'],
            'gender' => $params['gender'] ?? null,
            'company_name' => $params['company_name'],
            'email' => $params['email'] ?? null,
            'phone_number' => $params['phone_number'],
            'address' => $params['address'] ?? null,
            'status' => $params['status'] ?? true,
        ]);

        return $data;
    }

    public function customerUpdate($id, array $params = [])
    {
        $query = $this->getQuery();
        $data = $query->find($id);

        if (!$data) {
            throw new ModelNotFoundException("Customer with ID {$id} not found.");
        }

        $data->update([
            'name' => $params['name'] ?? $data->name,
            'gender' => $params['gender'] ?? $data->gender,
            'company_name' => $params['company_name'] ?? $data->company_name,
            'email' => $params['email'] ?? $data->email,
            'phone_number' => $params['phone_number'] ?? $data->phone_number,
            'address' => $params['address'] ?? $data->address,
            'status' => $params['status'] ?? $data->status,
        ]);

        return $data;
    }

    public function customerDelete($id)
    {
        $query = $this->getQuery();
        $data = $query->find($id);

        if (!$data) {
            throw new ModelNotFoundException("Customer with ID {$id} not found.");
        }

        $data->delete();

        return $data;
    }

    public function getCustomerById($id)
    {
        $query = $this->getQuery();
        $data = $query->find($id);

        if (!$data) {
            throw new ModelNotFoundException("Customer with ID {$id} not found.");
        }

        return $data;
    }
}