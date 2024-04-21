<?php

declare(strict_types=1);

namespace App\GraphQL\Directives;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Nuwave\Lighthouse\Execution\Arguments\ArgumentSet;
use Nuwave\Lighthouse\Schema\Directives\UpsertDirective;
use Nuwave\Lighthouse\Support\Contracts\ArgDirectiveForArray;
use Nuwave\Lighthouse\Support\Contracts\ArgResolver;

final class UpsertSyncDirective extends UpsertDirective implements ArgDirectiveForArray, ArgResolver
{
    // https://lighthouse-php.com/master/custom-directives/getting-started.html

    public static function definition(): string
    {
        return
            /** @lang GraphQL */
            '
directive @upsertSync on ARGUMENT_DEFINITION | INPUT_FIELD_DEFINITION
';
    }

    /**
     * @param  \Nuwave\Lighthouse\Execution\Arguments\ArgumentSet|array<\Nuwave\Lighthouse\Execution\Arguments\ArgumentSet>  $args
     * @param  \Illuminate\Database\Eloquent\Relations\Relation<\Illuminate\Database\Eloquent\Model>|null  $parentRelation
     *
     * @return \Illuminate\Database\Eloquent\Model|array<\Illuminate\Database\Eloquent\Model>
     */
    protected function executeMutation(Model $model, ArgumentSet|array $args, ?Relation $parentRelation = null): Model|array
    {
        throw_unless(is_array($args));
        throw_unless($parentRelation instanceof HasMany);

        $models = parent::executeMutation($model, $args, $parentRelation);

        // UpsertDirectiveの処理に加えて、リストに含まれていないモデルの削除も行う。
        $ids = collect($models)->pluck('id');
        $excludedModels = $parentRelation->whereNotIn('id', $ids)->get();
        $excludedModels->each->delete();

        return $models;
    }
}
