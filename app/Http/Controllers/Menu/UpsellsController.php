<?php namespace App\Http\Controllers\Menu;


use App\Model\Menu;
use App\Model\MenuItem;
use App\Model\MenuType;
use \App\Http\Controllers\SplickitApiCurlController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class UpsellsController extends SplickitApiCurlController
{

    //Load Sections and Mods to the UI
    public function getCategoryUpsell(MenuType $menuType)
    {
        try {
            $menuTypesRelated = $menuType->getUpsellsByMenuTypeRelatedToMenu(session('current_menu_id'));
            return response()->json($menuTypesRelated, 200);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json($exception->getMessage(), 404);
        }
    }

    public function createCategoryUpsell(Request $request, MenuItem $menuItem)
    {
        try {
            $data = $request->all();
            /** @var MenuItem $menuItem */
            $menuItem = $menuItem->find($data['item']['item_id']);

            foreach ($data['menu_types'] as $menu_type) {
                $menuItem->menuTypesUpsellRelated()->syncWithoutDetaching([$menu_type['menu_type_id']]);
            }
            return 1;
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json($exception->getMessage(), 404);
        }
    }

    public function deleteCategoryUpsell(Request $request, MenuItem $menuItem)
    {
        try {
            $data = $request->all();
            /** @var MenuItem $menuItem */
            $menuItem = $menuItem->find($data['item_id']);
            return $menuItem->menuTypesUpsellRelated()->detach($data['pivot']['menu_type_id']);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json($exception->getMessage(), 404);
        }
    }

    public function getCartUpsellByMenu(Menu $model)
    {
        try {
            $cart_upsells = $model->getCartUpsellsByMenu(session('current_menu_id'));
            return response()->json($cart_upsells, 200);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json($exception->getMessage(), 404);
        }
    }

    public function createCartUpsell(Request $request, Menu $model)
    {
        try {
            $data = $request->all();
            /** @var Menu $model */
            $model = $model->find(session('current_menu_id'));
            foreach ($data['items'] as $item) {
                $model->cartUpsells()->syncWithoutDetaching([$item['item_id']]);
            }
            return 1;
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json($exception->getMessage(), 404);
        }
    }

    public function deleteCartUpsell(Request $request, Menu $model)
    {
        try {
            $item_data = $request->all();
            /** @var Menu $menu */
            $menu = $model->find(session('current_menu_id'));
            return $menu->cartUpsells()->detach($item_data['item_id']);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json($exception->getMessage(), 404);
        }
    }
}
