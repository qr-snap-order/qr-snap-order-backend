<?php

namespace App\GraphQL\Directives;

use Arr;
use LogicException;
use Nuwave\Lighthouse\Execution\Arguments\ArgumentSet;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Support\Contracts\ArgDirectiveForArray;
use Nuwave\Lighthouse\Support\Contracts\ArgTransformerDirective;

class SetSortKeysDirective extends BaseDirective implements ArgDirectiveForArray, ArgTransformerDirective
{
    // https://lighthouse-php.com/master/custom-directives/getting-started.html

    public static function definition(): string
    {
        return
            /** @lang GraphQL */
            '
directive @setSortKeys on ARGUMENT_DEFINITION | INPUT_FIELD_DEFINITION
';
    }

    /**
     * Apply transformations on the value of an argument given to a field.
     *
     * @param  mixed  $argumentValue  the client given value
     *
     * @return array<int, ArgumentSet> the transformed value
     */
    public function transform($argumentValue): array
    {
        throw_unless(is_array($argumentValue), LogicException::class, 'Invalid set_sort_keys directive argument.');

        return Arr::map(
            $argumentValue,
            fn (ArgumentSet $value, int $idx) => $value->addValue('sort_key', $idx + 1)
        );
    }
}
