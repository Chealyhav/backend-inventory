<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $roleId = $this->route('id'); // get the role ID from the URL

        return [
            'name' => 'required|string|max:255|unique:roles,name,' . $roleId,
            'status' => 'required|in:0,1', // Only 0 or 1 are valid
        ];
    }
}
