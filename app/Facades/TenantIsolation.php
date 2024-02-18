<?php

namespace App\Facades;

use App\Services\DB\TenantIsolationService;
use Illuminate\Support\Facades\Facade;

/**
 * @see TenantIsolationService
 */
class TenantIsolation extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return TenantIsolationService::class;
    }
}
