<?php

namespace App\Repositories;

use App\Models\Menu;

class MenuRepository
{
    public function getAllMenu()
    {
        return Menu::with(['category' => function ($query) {
            $query->select('id', 'name');
        }])
            ->orderBy('is_available', 'desc')
            ->orderBy('name')
            ->get();
    }

    public function findById(int $id)
    {
        return Menu::with('category')
            ->findOrFail($id);
    }

    public function create(array $data)
    {
        $menu = Menu::create($data);
        return $menu;
    }

    public function update(int $id, array $data)
    {
        $menu = Menu::findOrFail($id);
        $menu->update($data);
        return $menu;
    }

    public function delete(int $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();
    }
}
