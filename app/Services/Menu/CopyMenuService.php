<?php

namespace App\Services\Menu;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuSection;

class CopyMenuService
{
    public function __invoke(Menu $menu): Menu
    {
        $menu->load(['menuSections.menuItems']);

        return $this->copyMenu($menu);
    }

    protected function copyMenu(Menu $menu): Menu
    {
        $replicatedMenu = $menu->replicate();
        $replicatedMenu->save();

        $menu->menuSections->each(
            fn (MenuSection $menuSection) => $this->copyMenuSection($menuSection, $replicatedMenu)
        );

        return $replicatedMenu;
    }

    protected function copyMenuSection(MenuSection $menuSection, Menu $parentMenu): MenuSection
    {
        $replicatedMenuSection = $menuSection->replicate();
        $replicatedMenuSection->menu_id = $parentMenu->id;
        $replicatedMenuSection->save();

        $menuSection->menuItems->each(
            fn (MenuItem $menuItem) => $this->copyMenuItem($menuItem, $replicatedMenuSection)
        );

        return $replicatedMenuSection;
    }

    protected function copyMenuItem(MenuItem $menuItem, MenuSection $parentMenuSection): MenuItem
    {
        $replicatedMenuItem = $menuItem->replicate();
        $replicatedMenuItem->menu_section_id = $parentMenuSection->id;
        $replicatedMenuItem->save();

        return $replicatedMenuItem;
    }
}
