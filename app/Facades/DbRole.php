<?php

namespace App\Facades;

use App\Services\DB\DbRoleService;
use Illuminate\Support\Facades\Facade;

/**
 * @see DbRoleService
 */
class DbRole extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return DbRoleService::class;
    }
}
