<?php

namespace App\Models\Concerns\Order;

use App\Models\Order;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Database\Eloquent\Builder;

/**
 * @mixin Order
 */
trait HasScope
{
    protected function scopeByDate(Builder $builder, string $data): void
    {
        $date = CarbonImmutable::parse($data);

        $builder->whereBetween('created_at', [$date->startOfDay(), $date->endOfDay()]);
    }

    protected function scopeByShop(Builder $builder, string $shopId): void
    {
        $builder->whereHas('shopTable.shop', fn(Builder $shopBuilder) => $shopBuilder->where('id', $shopId));
    }
}
