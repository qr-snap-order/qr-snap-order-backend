<?php

namespace App\Models\Concerns\EmployeeGroup;

use App\Models\Employee;
use App\Models\EmployeeGroup;
use App\Models\EmployeeGroupAssignment;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin EmployeeGroup
 */
trait HasRelation
{
    /**
     * @return BelongsTo<Tenant, EmployeeGroup>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class)->guardNull();
    }

    /**
     * @return BelongsToMany<Employee>
     */
    public function Employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_group_assignments')->using(EmployeeGroupAssignment::class);
    }
}
