<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class RegisterRelationMacroProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Relation::macro('guardNull', function() {
            /** @var BelongsTo<Model, Model>|HasOne<Model> $relation */
            $relation = $this;

            return $relation->withDefault(
                fn () => throw (new ModelNotFoundException)->setModel($relation->getRelated()::class)
            );
        });
    }
}
