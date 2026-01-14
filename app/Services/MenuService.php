<?php

namespace App\Services;

use App\Repositories\MenuRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MenuService
{
    private MenuRepository $menuRepository;

    public function __construct(MenuRepository $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    public function getAllMenu()
    {
        $menus = $this->menuRepository->getAllMenu();
        return $menus->groupBy(function ($menu) {
            return $menu->category->name;
        });
    }

    public function getMenuById(int $id)
    {
        $menu = $this->menuRepository->findById($id);
        return $menu;
    }

    public function createMenu(array $data)
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $path = $data['image']->store('menus', 'public');
            $data['image'] = $path;
        }
        return $this->menuRepository->create($data);
    }

    public function updateMenu(int $id, array $data)
    {
        $menu = $this->menuRepository->findById($id);
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $path = $data['image']->store('menus', 'public');
            $data['image'] = $path;
        }
        return $this->menuRepository->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->menuRepository->delete($id);
    }
}
