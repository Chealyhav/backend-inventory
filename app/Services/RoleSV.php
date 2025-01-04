<?php

namespace App\Services;

use App\Models\Role;

class RoleSV  extends BaseService
{
    public function  getQuery()
    {
      return Role::query();
    }
    

}
