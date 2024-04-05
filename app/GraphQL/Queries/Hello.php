<?php

namespace App\GraphQL\Queries;

use App\Facades\Context;

final class Hello
{
    // @phpstan-ignore-next-line
    public function __invoke(mixed $root, array $args): string
    {
        return 'hello';
    }
}
