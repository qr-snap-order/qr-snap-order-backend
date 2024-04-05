<?php

namespace App\Services\DB;

use App\Facades\DbConfig;
use App\Facades\DbPolicy;
use App\Facades\DbRole;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

/**
 * テナントごとに行単位のアクセス制御を行うためのサービス。
 */
class TenantIsolationService
{
    public function __construct(
        protected readonly string $tenantRole = 'tenant_isolation_role',
        protected readonly string $tenantPolicy = 'tenant_isolation_policy',
        protected readonly string $sessionTenantIdConfigKey = 'session.tenant_id',
    ) {
    }

    public function createTenantRoleIfNotExists(): void
    {
        if (!DbRole::existsRole($this->tenantRole)) {
            DbRole::createRole($this->tenantRole);
        };
    }

    public function dropTenantRoleIfExists(): void
    {
        if (DbRole::existsRole($this->tenantRole)) {
            DbRole::dropRole($this->tenantRole);
        };
    }

    /**
     * 対象の行への操作(CRUD)権限をTenantRoleへ付与する。
     * setSessionTenantRoleでセットされたTenantIdと一致する行のみ操作可能となる。
     *
     * @param string $table
     * @param string $tenantIdColumn
     * @return void
     */
    public function grantIsolationRowAccessToTenantRole(string $table, string $tenantIdColumn): void
    {
        DbRole::grantAllPrivilegesToRole($this->tenantRole, $table);

        DbPolicy::enableRowLevelSecurity($table);

        DbPolicy::createPolicy(
            $this->tenantPolicy,
            $table,
            "{$tenantIdColumn} = " . DbConfig::currentSettingStatement($this->sessionTenantIdConfigKey) . "::uuid"
        );
    }

    /**
     * grantIsolationRowAccessToTenantRoleで付与した権限を剥奪する。
     *
     * @param string $table
     * @return void
     */
    public function revokeIsolationRowAccessFromTenantRole(string $table): void
    {
        DbRole::revokeAllPrivilegesFromRole($this->tenantRole, $table);

        DbPolicy::disableRowLevelSecurity($table);

        DbPolicy::dropPolicy($this->tenantPolicy, $table);
    }

    /**
     * @param string $table
     * @return void
     */
    public function grantAllPrivileges(string $table): void
    {
        DbRole::grantAllPrivilegesToRole($this->tenantRole, $table);
    }

    /**
     * @param string $table
     * @return void
     */
    public function revokeAllPrivileges(string $table): void
    {
        DbRole::revokeAllPrivilegesFromRole($this->tenantRole, $table);
    }

    /**
     * TenantRoleをセットする。
     * 指定されたTenantに紐づく行（grantIsolationRowAccessToTenantRoleで指定）のみ操作(CRUD)可能となる。
     *
     * @param string $tenantId
     * @return void
     */
    public function setSessionTenant(string $tenantId): void
    {
        DbRole::setRole($this->tenantRole);

        DbConfig::setConfig($this->sessionTenantIdConfigKey, $tenantId);
    }

    /**
     * @return void
     */
    public function clearSessionTenant(): void
    {
        DbRole::setRole(config('database.connections.pgsql.username'));

        DbConfig::setConfig($this->sessionTenantIdConfigKey, '');
    }

    /**
     * @return Expression
     */
    public function sessionTenantIdExpression(): Expression
    {
        return DB::raw(
            DbConfig::currentSettingStatement($this->sessionTenantIdConfigKey) . '::uuid'
        );
    }
}
