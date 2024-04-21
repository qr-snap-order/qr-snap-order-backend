<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Menu;
use App\Services\Menu\CopyMenuService;

final readonly class CopyMenu
{
    public function __construct(
        protected readonly CopyMenuService $copyMenuService
    ) {
    }

    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): Menu
    {
        $id = $args['id']; // @phpstan-ignore-line

        /** @var Menu $menu */
        $menu = Menu::findOrFail($id);

        /** @var Menu $copiedMenu */
        $copiedMenu = $this->copyMenuService->__invoke($menu)->fresh();

        return $copiedMenu;
    }
}
