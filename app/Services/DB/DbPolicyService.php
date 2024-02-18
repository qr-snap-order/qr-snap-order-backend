<?php

namespace App\Services\DB;

use Illuminate\Support\Facades\DB;

class DbPolicyService
{
    public function enableRowLevelSecurity(string $table): void
    {
        DB::statement("ALTER TABLE {$table} ENABLE ROW LEVEL SECURITY");
    }

    public function disableRowLevelSecurity(string $table): void
    {
        DB::statement("ALTER TABLE {$table} DISABLE ROW LEVEL SECURITY");
    }

    public function createPolicy(string $policy, string $table, string $using, ?string $check = null): void
    {
        $check ??= $using;
        DB::statement("CREATE POLICY {$policy} ON {$table} USING ({$using}) WITH CHECK ($check)");
    }

    public function dropPolicy(string $policy, string $table): void
    {
        DB::statement("DROP POLICY {$policy} ON {$table}");
    }
}
