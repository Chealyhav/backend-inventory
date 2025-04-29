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
        //pagination
        $total = $query->count();
        $limit = $params['limit'] ?? 10;
        $page = $params['page'] ?? 1;
        $totalPage = ceil($total / $limit);
        $nextPage = $page + 1 ?? 0;
        $prevPage = $page - 1 ?? 0;

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
