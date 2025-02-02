<?php

namespace App\Traits;

use Illuminate\Support\Facades\Gate;

trait PermissionsTrait
{
    public function hasAccessToModule($moduleName)
    {
        return Gate::allows('module-access', $moduleName);
    }
}
