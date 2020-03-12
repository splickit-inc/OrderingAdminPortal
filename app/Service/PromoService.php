<?php
/**
 * Created by PhpStorm.
 * User: diego.rodriguez
 * Date: 9/20/17
 * Time: 10:19 AM
 */

namespace App\Service;

use App\Model\MenuItemSizeMap;

class PromoService extends BaseService {

    public $data;

    public function buyObjects() {
        switch ($this->data['bogo_menu_selections']['buy_type']) {
            case 'menu_type':
                return $this->buildSmawMenuTypes($this->data['bogo_menu_selections']['selected_buy_sections']);
        break;
            case 'item':
                return $this->buildSmawMenuTypeItems($this->data['bogo_menu_selections']['selected_buy_section_items']);
        break;
            case 'menu_type_sizes':
                return $this->buildSmawMenuTypeSizes($this->data['bogo_menu_selections']['selected_buy_section_items_sizes']);
        break;
            case 'item_size':
                return $this->buildSmawItemSizes($this->data['bogo_menu_selections']['selected_buy_section_items_sizes']);
        break;
            default:
                return ['Category-'.$this->data['buy_category']];
        }
    }

    public function getObjects() {
        switch ($this->data['bogo_menu_selections']['get_type']) {
            case 'menu_type':
                return $this->buildSmawMenuTypes($this->data['bogo_menu_selections']['selected_get_sections']);
        break;
            case 'item':
                return $this->buildSmawMenuTypeItems($this->data['bogo_menu_selections']['selected_get_section_items']);
        break;
            case 'menu_type_sizes':
                return $this->buildSmawMenuTypeSizes($this->data['bogo_menu_selections']['selected_get_section_items_sizes']);
        break;
            case 'item_size':
                return $this->buildSmawItemSizes($this->data['bogo_menu_selections']['selected_get_section_items_sizes']);
        break;
            default:
                return ['Category-'.$this->data['get_category']];
        }
    }

    public function buildSmawCategories($categories) {
        $response = [];

        foreach ($categories as $category) {
            //Add All Category Types Later, Now Smaw can only except Entre
            //$response[] = 'Category-'.$category['type_id_value'];
            $response[] = $category['type_id_value'];
        }
        return $response;
    }

    public function buildSmawMenuTypes($menu_types) {
        $response = [];

        foreach ($menu_types as $menu_type) {
            $response[] = 'Menu_Type-'.$menu_type['menu_type_id'];
        }
        return $response;
    }

    public function buildSmawMenuTypeItems($items) {
        $response = [];

        foreach ($items as $item) {
            $response[] = 'Item-'.$item['item_id'];
        }
        return $response;
    }

    public function buildSmawMenuTypeSizes($sizes) {
        $response = [];

        foreach ($sizes as $size) {
            $response[] = 'Size-'.$size['size_id'];
        }
        return $response;
    }

    public function buildSmawItemSizes($item_sizes) {
        $response = [];

        foreach ($item_sizes as $item_size) {
            $masterItemSize = MenuItemSizeMap::find($item_size['item_size_id']);
            $allItemSizes = MenuItemSizeMap::where('item_id','=', $masterItemSize->item_id)
                                          ->where('size_id','=', $masterItemSize->size_id)
                                          ->where('active','=', 'Y')
                                          ->get()->toArray();

            foreach ($allItemSizes as $merchantLevelItemSize) {
                $response[] = 'Item_Size-'.$merchantLevelItemSize['item_size_id'];
            }
        }
        return $response;
    }

    public function buildSmawMenuEntityArray($menu_entities) {
        $categories = $this->buildSmawCategories($menu_entities['categories']);
        $menu_types = $this->buildSmawMenuTypes($menu_entities['sections']);
        $menu_type_sizes = $this->buildSmawMenuTypeSizes($menu_entities['section_sizes']);
        $menu_type_items = $this->buildSmawMenuTypeItems($menu_entities['items']);
        $menu_type_item_sizes = $this->buildSmawItemSizes($menu_entities['section_items_sizes']);

        return array_merge($categories, $menu_types, $menu_type_sizes, $menu_type_items, $menu_type_item_sizes);
    }
}