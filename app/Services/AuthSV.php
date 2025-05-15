<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWTAuth as JWTAuthFactory;
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
        $roleName = DB::table('roles')->where('id', $user->role_id)->value('name');


        $token = JWTAuth::fromUser($user);

        if (!$token) {
            throw new Exception('Token not found');
        }
        $data = [
            'id' => $user->id,
            'username' => $user->username,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'dob' => $user->dob,
            'bio' => $user->bio,
            'gender' => $user->gender,
            'profile_picture' => $user->profile_picture,
            'email_verified_at' => $user->email_verified_at,
            'status' => $user->status,
            'role_id' => $user->role_id,
            'role_name' => $roleName,
            'deleted_at' => $user->deleted_at,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];

        return [
            'user' => $data,
            'access_token' => $token,
            'token_type' => 'bearer',
            //format expires_in to human readable date  to local timezone and  format to Y-m-d H:i:s pm or am  full date time
            'expires_in' => (new \DateTime('+1 hour', new \DateTimeZone('UTC')))
                ->setTimezone(new \DateTimeZone('Asia/Phnom_Penh'))
                ->format('Y-m-d h:i:s A'),
            'expires' => JWTAuth::factory()->getTTL() * 60, // 1 hour
            'number_expires' => '1 hour',
        ];
    }

    // Refresh JWT token
    public function refresh()
    {
        try {
            $token = JWTAuth::refresh(JWTAuth::getToken());
            return [
                'refresh_token' => $token,
                'token_type' => 'bearer',
                //expire  7 days from now
                'expires_date' => JWTAuth::factory()->getTTL() * 60 * 24 * 7, // 7 days
                'expires_in' => (new \DateTime('+7 days', new \DateTimeZone('UTC')))
                    ->setTimezone(new \DateTimeZone('Asia/Phnom_Penh'))
                    ->format('Y-m-d h:i:s A'),
                'number_expires' => '7 days',
            ];
        } catch (TokenExpiredException $e) {
            throw new Exception('Token expired');
        }
    }
    // Logout user
    public function logout($token)
    {
        try {
            if (!$token) {
                return ['message' => 'Token is required for logout'];
            }
           return JWTAuth::setToken($token)->invalidate(true);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return ['message' => 'Token expired'];
        } catch (JWTException $e) {
            return ['message' => 'Failed to logout, please try again'];
        }

    }


    // Reset password using refresh_token
    public function resetPassword($refresh_token, $new_password)
    {
        try {
            // Get the user from the refresh token
            $user = JWTAuth::setToken($refresh_token)->toUser();

            if (!$user) {
                throw new Exception('User not found');
            }

            // Ensure the new password is different from the old password
            if (Hash::check($new_password, $user->password)) {
                throw new Exception('New password cannot be the same as the old password');
            }

            // Update the user's password
            $user->password = bcrypt($new_password);
            $user->save();

            return response()->json(['message' => 'Password reset successfully']);
        } catch (Exception $e) {
            throw new Exception('Failed to reset password: ' . $e->getMessage());
        }
    }



    //get profile user by token
    public function getProfile($token = null)
    {
        try {
            if ($token) {
                JWTAuth::setToken($token);
            }

            $user = JWTAuth::parseToken()->authenticate();


            if (!$user) {
                throw new Exception('User not found');
            }

            //make change role_id  to role_name
            $roleName = DB::table('roles')->where('id', $user->role_id)->value('name');
            $user->role_name = $roleName;

            return [
                'id' => $user->id,
                'username' => $user->username,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'dob' => $user->dob,
                'bio' => $user->bio,
                'role_name' => $user->role_name,
                'profile_picture' => $user->profile_picture,
                'email_verified_at' => $user->email_verified_at,
                'status' => $user->status,
                'role_id' => $user->role_id,
                'deleted_at' => $user->deleted_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'created_by' => $user->created_by,
                'updated_by' => $user->updated_by,
                'deleted_by' => $user->deleted_by,
                 //expire  7 days from now
                 'expires_date' => JWTAuth::factory()->getTTL() * 60 * 24 * 7, // 7 days
                 'expires_in' => (new \DateTime('+7 days', new \DateTimeZone('UTC')))
                     ->setTimezone(new \DateTimeZone('Asia/Phnom_Penh'))
                     ->format('Y-m-d h:i:s A'),
                 'number_expires' => '7 days',
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to get profile: ' . $e->getMessage());
        }
    }
}
