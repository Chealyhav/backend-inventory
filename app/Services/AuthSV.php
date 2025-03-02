<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\DB;

class AuthSV extends BaseService
{
    public function getQuery()
    {
        return User::query();
    }

    // Respond with refresh token
    protected function respondWithRefreshToken($token)
    {
        return response()->json([
            'status' => 'success',
            'token' => $token
        ]);
    }

    // Create user with role and generate JWT token
    public function UserCreate(array $params = [])
    {
        $query = $this->getQuery();
        $user = $query->create([
            'username' => $params['username'], // Include username
            'first_name' => $params['first_name'], // Include first name
            'last_name' => $params['last_name'], // Include last name
            'email' => $params['email'],
            'phone' => $params['phone'], // Include phone
            'dob' => $params['dob'], // Include date of birth
            'bio' => $params['bio'], // Include bio
            'gender' => $params['gender'], // Include gender
            'profile_picture' => $params['profile_picture'], // Include profile picture
            'password' => bcrypt($params['password']),
            'status' => $params['status'] ?? 1,
            'role_id' => $params['role_id'],
            'created_by' => Auth::id(), // Safe Auth call
        ]);

        $token = JWTAuth::fromUser($user);
        //get role name by role id
        $role = DB::table('roles')->where('id', $user->role_id)
            ->select('name')
            ->first();

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in_second' => JWTAuth::factory()->getTTL() * 60,
            // 'role' => $role,
        ];
    }

    // User login and JWT generation
    public function UserLogin(array $params = [])
    {
        $query = $this->getQuery();
        $user = $query->where('email', $params['email'])->first();

        if (!$user) {
            throw new Exception('User not found');
        }

        if (!Hash::check($params['password'], $user->password)) {
            throw new Exception('Invalid password');
        }

        //get role name by role id
        $role = DB::table('roles')->where('id', $user->role_id)
            ->select('name')
            ->first();

        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in_second' => JWTAuth::factory()->getTTL() * 60,
            'role' => $role,

        ];
    }

    // Refresh JWT token
    public function refresh()
    {
        try {
            $token = JWTAuth::refresh(JWTAuth::getToken());
            return [

                'token' => $token,
                'token_type' => 'bearer',

                //expire  7 days from now
                'expires_date' => JWTAuth::factory()->getTTL() * 60 * 24 * 7,
            ];
        } catch (TokenExpiredException $e) {
            throw new Exception('Token expired');
        }
    }
    // Logout user
    public function logout()
    {
        $token = JWTAuth::invalidate(JWTAuth::getToken());

        if (!$token) {
            throw new Exception('Token not found');
        }
        return  $token;
    }
    //resetPassword
    public function resetPassword($token, $password)
    {
        $user = JWTAuth::toUser($token);
        $user->password = bcrypt($password);
        $user->save();
        if (!$user) {
            throw new Exception('User not found');
        }
        if ($user->password === $password) {
            throw new Exception('New password cannot be the same as the old password');
        }
        return $user;
    }
}
