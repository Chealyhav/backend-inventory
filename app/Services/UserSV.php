<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Services\CloudinarySv;

class UserSV
{
    protected $cloudinarySv;

    public function __construct(CloudinarySv $cloudinarySv)
    {
        $this->cloudinarySv = $cloudinarySv;
    }

    public function getQuery()
    {
        return User::query();
    }

    /**
     * Get User List with Pagination
     */
    public function getUserList(array $params)
    {
        $query = $this->getQuery()
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->select(
                'users.id',
                'users.username',
                'users.first_name',
                'users.last_name',
                'users.phone',
                'users.dob',
                'users.email',
                'users.role_id',
                'users.role_id',
                'roles.name as role_name',
                'users.profile_picture',
                'users.status',
                'users.created_at',
                'users.updated_at'
            );

        if (!empty($params['search'])) {
            $query->where('users.name', 'LIKE', '%' . $params['search'] . '%');
        }

        if (!empty($params['role_id'])) {
            $query->where('users.role_id', $params['role_id']);
        }

        $limit = $params['limit'] ?? 10;
        $page = $params['page'] ?? 1;
        $offset = ($page - 1) * $limit;

        $total = $query->count();
        $data = $query->skip($offset)->take($limit)->get();
        $totalPage = ceil($total / $limit);
        $nextPage = $page + 1;
        $prevPage = $page - 1;
        $result = $query->get();
        return [
            'total' => $total,
            'totalPage' => $totalPage,
            'nextPage' => $nextPage,
            'prevPage' => $prevPage,
            'currentPage' => $page,
            'limit' => $limit,
            'data' => $result,
        ];
    }

    /**
     * Create New User
     */
    public function createUser(array $params)
    {
        // Upload profile picture if provided
        if (isset($params['profile_picture'])) {
            $params['profile_picture'] = $this->cloudinarySv->uploadImage($params['profile_picture']);
        }
        if (isset($params['password'])) {
            $params['password'] = Hash::make($params['password']);
        }
        $user = $this->getQuery()->create($params);
        return $user;
    }

    /**
     * Get User By ID
     */
    public function getUserById($id)
    {
        if (empty($id)) {
            throw new \Exception('User ID is required.');
        }
        return User::with('role')->findOrFail($id);
    }

    /**
     * Update User
     */
    public function updateUser($id, array $params)
    {
        $user = User::findOrFail($id);

        // If a new image is uploaded and it's different from the current image
        if (isset($params['profile_picture']) && $params['profile_picture'] !== $user->profile_picture) {
            // Delete the old image from Cloudinary if exists
            if ($user->profile_picture) {
                $currentPublicId = $this->cloudinarySv->extractPublicIdFromUrl($user->profile_picture);
                $this->cloudinarySv->deleteImage($currentPublicId);
            }

            // Upload the new image to Cloudinary
            $params['profile_picture'] = $this->cloudinarySv->uploadImage($params['profile_picture']);
        }

        $user->update([
            'name' => $params['name'] ?? $user->name,
            'email' => $params['email'] ?? $user->email,
            'profile_picture' => $params['profile_picture'] ?? $user->profile_picture,
            'role_id' => $params['role_id'] ?? $user->role_id,
            'status' => $params['status'] ?? $user->status,
            'updated_by' => Auth::id(),
        ]);

        return $user;
    }

    /**
     * Delete User
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Delete profile picture from Cloudinary if exists
        if ($user->profile_picture) {
            $currentPublicId = $this->cloudinarySv->extractPublicIdFromUrl($user->profile_picture);
            $this->cloudinarySv->deleteImage($currentPublicId);
        }

        return $user->delete();
    }
}
