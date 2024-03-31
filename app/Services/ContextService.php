<?php

namespace App\Services;

use App\Facades\TenantIsolation;
use App\Models\Tenant;
use Closure;

class ContextService
{
    protected ?Tenant $tenant = null;

    public function setTenant(?Tenant $tenant): void
    {
        if ($tenant) {
            TenantIsolation::setSessionTenant($tenant->id);
        } else {
            TenantIsolation::clearSessionTenant();
        }

        $this->tenant = $tenant;
    }

    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function hasTenant(): bool
    {
        return $this->tenant !== null;
    }

    public function callWithTenant(?Tenant $tenant, Closure $callback): mixed
    {
        $previousTenant = $this->getTenant();

        try {
            $this->setTenant($tenant);

            return $callback();
        } finally {
            $this->setTenant($previousTenant);
        }
    }
}
