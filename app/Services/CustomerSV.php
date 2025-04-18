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

    // public function getAllCustomers($params = [])
    // {
    //     $query = $this->getQuery()->from('customers as c');

    //     // Search filter
    //     if (!empty($params['search'])) {
    //         $searchTerm = '%' . $params['search'] . '%';
    //         $query->where(function ($q) use ($searchTerm) {
    //             $q->where('c.name', 'LIKE', $searchTerm)
    //                 ->orWhere('c.company_name', 'LIKE', $searchTerm)
    //                 ->orWhere('c.email', 'LIKE', $searchTerm)
    //                 ->orWhere('c.phone_number', 'LIKE', $searchTerm);
    //         });
    //     }

    //     // Gender filter
    //     if (!empty($params['gender'])) {
    //         $query->where('c.gender', $params['gender']);
    //     }

    //     // Status filter - only apply if status is explicitly provided
    //     if (isset($params['status'])) {
    //         // Handle string 'true'/'false' or boolean true/false
    //         $status = is_string($params['status']) 
    //             ? filter_var($params['status'], FILTER_VALIDATE_BOOLEAN)
    //             : (bool)$params['status'];
    //         $query->where('c.status', $status);
    //     }

    //     // Sorting
    //     if (!empty($params['order_by'])) {
    //         $query->orderBy($params['order_by'], $params['order'] ?? 'asc');
    //     }

    //     // Pagination count
    //     $total = $query->count();

    //     // Pagination
    //     $limit = $params['limit'] ?? 10;
    //     $page = $params['page'] ?? 1;
    //     $offset = ($page - 1) * $limit;
    //     $totalPage = ceil($total / $limit);
    //     $nextPage = $page < $totalPage ? $page + 1 : 0;
    //     $prevPage = $page > 1 ? $page - 1 : 0;

    //     $data = $query->offset($offset)->limit($limit)->get();

    //     return [
    //         'total' => $total,
    //         'totalPage' => $totalPage,
    //         'nextPage' => $nextPage,
    //         'prevPage' => $prevPage,
    //         'currentPage' => $page,
    //         'limit' => $limit,
    //         'data' => $data,
    //     ];
    // }

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

        // Get total count before pagination
        $total = $query->count();
        $limit = $params['limit'] ?? 10;
        $page = $params['page'] ?? 1;
        $totalPage = ceil($total / $limit);
        $nextPage = $page + 1 ?? 0;
        $prevPage = $page - 1 ?? 0;

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

    /**
     * Retrieve a customer by its ID.
     *
     * @param int $id The ID of the customer to retrieve.
     *
     * @return \App\Models\Customer|null The customer model if found, otherwise null.
     */
    public function getCustomerById($id)
    {
        $customer = $this->getQuery()->find($id);
    
        if (!$customer) {
            throw new \RuntimeException("Customer with ID {$id} not found");
        }
    
        return $customer;
    }

    /**
     * Create a new customer.
     *
     * This function creates a new customer record in the database using the provided parameters.
     * The 'name', 'company_name', and 'phone_number' fields are required.
     *
     * @param array $params An associative array containing customer attributes:
     *                      - 'name' (string): The name of the customer. Required.
     *                      - 'company_name' (string): The company name of the customer. Required.
     *                      - 'email' (string): The email address of the customer. Optional.
     *                      - 'phone_number' (string): The phone number of the customer. Required.
     *                      - 'address' (string): The address of the customer. Optional.
     *                      - 'status' (bool): The status of the customer. Defaults to true.
     * @return \App\Models\Customer The created customer model.
     * @throws \InvalidArgumentException If required fields are missing.
     */

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

        $data = $query->create([
            'name' => $params['name'],
            'gender' => $params['gender'] ?? null,
            'company_name' => $params['company_name'],
            'email' => $params['email'] ?? null,
            'phone_number' => $params['phone_number'],
            'address' => $params['address'] ?? null,
            'status' => $params['status'] ?? true,
        ]);

        return $customer;
    }

/**
 * Update a customer's information.
 *
 * @param int $id The ID of the customer to update.
 * @param array $params The new attributes for the customer.
 * @return \App\Models\Customer The updated customer model.
 * @throws ModelNotFoundException If no customer is found with the given ID.
 */

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
