<?php

namespace App\Facades;

use App\Services\ContextService;
use Illuminate\Support\Facades\Facade;

/**
 * @see ContextService
 */
class Context extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return ContextService::class;
    }
}
