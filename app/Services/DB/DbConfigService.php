<?php

namespace App\Services\DB;

use Illuminate\Support\Facades\DB;

class DbConfigService
{
    public function setConfig(string $key, string $value): void
    {
        DB::statement("SELECT set_config('{$key}', '{$value}', false)");
    }

    public function currentSettingStatement(string $key): string
    {
        return "current_setting('{$key}')";
    }
}
