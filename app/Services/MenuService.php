<?php

namespace App\Services;

use App\Repositories\MenuRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MenuService {
    private MenuRepository $menuRepository;

    public function __construct(MenuRepository $menuRepository) {
        $this->menuRepository = $menuRepository;
    }

    public function getAvailableMenu() {
        return $this->menuRepository->getAvailableMenu();
    }

    public function createMenu(array $data) {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $path = $data['image']->store('menus', 'public');
            $data['image'] = $path;
        }
        return $this->menuRepository->create($data);
    }

    public function updateMenu(int $id, array $data) {
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

    public function changeStatus(int $id) {
        return $this->menuRepository->update($id, ['status', false]);
    }
}
