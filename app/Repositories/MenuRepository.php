<?php

namespace App\Repositories;

use App\Models\Menu;

class MenuRepository
{
    public function getAvailableMenu()
    {
        return Menu::where('is_available', true)
            ->with('category')
            ->get();
    }

    public function findById(int $id) {
        return Menu::with('category')->findOrFail($id);
    }

    public function create(array $data) {
        $menu = Menu::create($data);
        return $menu;
    }

    public function update(int $id, array $data) {
        $menu = Menu::findOrFail($id);
        $menu->update($data);
        return $menu;
    }
}
