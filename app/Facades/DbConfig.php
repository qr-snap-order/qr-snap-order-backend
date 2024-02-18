<?php

namespace App\Facades;

use App\Services\DB\DbConfigService;
use Illuminate\Support\Facades\Facade;

/**
 * @see DbConfigService
 */
class DbConfig extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return DbConfigService::class;
    }
}
