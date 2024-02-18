<?php

namespace App\Facades;

use App\Services\DB\DbPolicyService;
use Illuminate\Support\Facades\Facade;

/**
 * @see DbPolicyService
 */
class DbPolicy extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return DbPolicyService::class;
    }
}
