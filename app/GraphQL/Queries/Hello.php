<?php

namespace App\GraphQL\Queries;

use App\Facades\Context;

final class Hello
{
    public function __invoke(mixed $root, array $args): string
    {
        return 'hello';
    }
}
