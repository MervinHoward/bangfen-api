<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuRequest;
use App\Http\Resources\MenuResource;
use App\Services\MenuService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    private MenuService $menuService;

    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groupMenus = $this->menuService->getAllMenu();
        $data = $groupMenus->map(function ($menus, $categoryName) {
            return MenuResource::collection($menus);
        });
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MenuRequest $request)
    {
        $menu = $this->menuService->createMenu($request->validated());
        return response()->json(new MenuResource($menu), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            $menu = $this->menuService->getMenuById($id);
            return response()->json(new MenuResource($menu));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Menu tidak ditemukan.'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MenuRequest $request, int $id)
    {
        try {
            $menu = $this->menuService->updateMenu($id, $request->validated());
            return response()->json(new MenuResource($menu));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Menu tidak ditemukan.'
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $this->menuService->delete($id);
            return response()->json([
                'message' => 'Menu berhasil dihapus.'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Menu tidak ditemukan.'
            ], 404);
        }
    }
}
