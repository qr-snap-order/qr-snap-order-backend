<?php

namespace App\Services\DB;

use Illuminate\Support\Facades\DB;

class DbRoleService
{
    public function setRole(string $role): void
    {
        DB::statement("SET ROLE {$role}");
    }

    public function existsRole(string $role): bool
    {
        $result = DB::selectOne("SELECT * FROM pg_roles WHERE rolname = '{$role}'");
        return $result !== null;
    }

    public function createRole(string $role): void
    {
        DB::statement("CREATE ROLE {$role} WITH LOGIN");
    }

    public function dropRole(string $role): void
    {
        DB::statement("DROP ROLE {$role}");
    }

    public function grantAllPrivilegesToRole(string $role, string $table): void
    {
        DB::statement("GRANT ALL PRIVILEGES ON {$table} TO {$role}");
    }

    public function revokeAllPrivilegesFromRole(string $role, string $table): void
    {
        DB::statement("REVOKE ALL PRIVILEGES ON {$table} FROM {$role}");
    }
}
