<?php

declare(strict_types=1);

namespace App\GraphQL\Directives;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Support\Contracts\ArgDirectiveForArray;
use Nuwave\Lighthouse\Support\Contracts\ArgResolver;

final class ConnectSyncDirective extends BaseDirective implements ArgDirectiveForArray, ArgResolver
{
    // https://lighthouse-php.com/master/custom-directives/getting-started.html

    public static function definition(): string
    {
        return
            /** @lang GraphQL */
            '
directive @connectSync (
  """
  Specify the relationship method name in the model class,
  if it is named different from the field in the schema.
  """
  relation: String
 ) on ARGUMENT_DEFINITION | INPUT_FIELD_DEFINITION
';
    }

    public function __invoke($parent, $args): void
    {
        throw_unless($parent instanceof Model);
        throw_unless(is_array($args));

        $relationName = $this->directiveArgValue(
            'relation',
            // Use the name of the argument if no explicit relation name is given
            $this->nodeName(),
        );

        $relation = $parent->{$relationName}();
        assert($relation instanceof BelongsToMany);

        $relation->sync(
            $this->generateRelationArray($args)
        );
    }

    /**
     * @param array<mixed> $values
     *
     * @return array<mixed>
     */
    protected function generateRelationArray(array $values): array
    {
        if (empty($values)) {
            return [];
        }

        if (is_array($values[0])) {
            $relationArray = [];
            foreach ($values as $value) {
                $id = Arr::pull($value, 'id');
                $relationArray[$id] = $value;
            }

            return $relationArray;
        }

        // The default case is simply a flat array of IDs which we don't have to transform
        return $values;
    }
}
