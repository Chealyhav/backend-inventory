<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Services\CloudinarySV;

class UserSV extends BaseService
{
    protected $cloudinarySv;

    public function __construct()
    {
        $this->cloudinarySv = new CloudinarySV();
    }

    public function getQuery()
    {
        return User::query();
    }

    public function getAllUsers($params = [])
    {
        $query = DB::table('users as u')
            ->leftJoin('roles as r', 'u.role_id', '=', 'r.id')
            ->select(
                'u.id',
                'u.username',
                'u.first_name',
                'u.last_name',
                'u.email',
                'u.phone',
                'u.dob',
                'u.bio',
                'u.gender',
                'u.profile_picture',
                'u.created_at',
                'u.updated_at',
                'u.deleted_at',
                'u.status',
                DB::raw('r.name as roleName')
            );

        // Search functionality
        if (!empty($params['search'])) {
            $searchTerm = '%' . $params['search'] . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('u.username', 'LIKE', $searchTerm)
                    ->orWhere('r.name', 'LIKE', $searchTerm)
                    ->orWhere('u.first_name', 'LIKE', $searchTerm)
                    ->orWhere('u.last_name', 'LIKE', $searchTerm);
            });
        }

        // Apply filters
        if (!empty($params['filter_by']) && is_array($params['filter_by'])) {
            foreach ($params['filter_by'] as $column => $value) {
                $query->where("u.$column", $value);
            }
        }
        // Sorting
        if (!empty($params['order_by'])) {
            $query->orderBy($params['order_by'], $params['order_direction'] ?? 'asc');
        }
        //role_id is
        if (!empty($params['role_id'])) {
            $query->where('u.role_id', $params['role_id']);
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

        $users = $query->offset($offset)->limit($limit)->get();

        return [
            'total' => $total,
            'totalPage' => $totalPage,
            'nextPage' => $nextPage,
            'prevPage' => $prevPage,
            'currentPage' => $page,
            'limit' => $limit,
            'data' => $users,
        ];
    }

    public function getUserById($id)
    {
        return User::find($id);
    }

    public function UserCreate(array $params = [])
    {
        $query = $this->getQuery();

        if (!empty($params['profile_picture'])) {
            $params['profile_picture'] = $this->cloudinarySv->uploadImage($params['profile_picture']);
        }

        $user = $query->create([
            'username' => data_get($params, 'username'),
            'first_name' => data_get($params, 'first_name'),
            'last_name' => data_get($params, 'last_name'),
            'email' => data_get($params, 'email'),
            'phone' => data_get($params, 'phone'),
            'dob' => data_get($params, 'dob'),
            'bio' => data_get($params, 'bio'),
            'gender' => data_get($params, 'gender'),
            'profile_picture' => data_get($params, 'profile_picture'),
            'password' => bcrypt(data_get($params, 'password')),
            'status' => data_get($params, 'status', 1),
            'role_id' => data_get($params, 'role_id'),
        ]);

        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in_seconds' => JWTAuth::factory()->getTTL() * 60,
        ];
    }

    public function UserUpdate($id, array $params = [])
    {
        $query = $this->getQuery();
        $user = $query->find($id);
        if (!$user) {
            throw new ModelNotFoundException('User not found.');
        }

        // If a new image is uploaded and it's different from the current image
        if (!empty($params['profile_picture']) && $params['profile_picture'] !== $user->profile_picture) {
            if ($user->profile_picture) {
                $currentPublicId = $this->cloudinarySv->extractPublicIdFromUrl($user->profile_picture);
                $this->cloudinarySv->deleteImage($currentPublicId);
            }
            $params['profile_picture'] = $this->cloudinarySv->uploadImage($params['profile_picture']);
        }

        $user->update($params);
        return $user;
    }

    public function UserDelete($id)
    {
        $user = $this->getUserById($id);
        if (!$user) {
            throw new ModelNotFoundException('User not found.');
        }

        // Ensure the user has a profile picture to delete
        if ($user->profile_picture) {
            $currentPublicId = $this->cloudinarySv->extractPublicIdFromUrl($user->profile_picture);
            $this->cloudinarySv->deleteImage($currentPublicId);
        }

        $user->delete();
        return $user;
    }
}
