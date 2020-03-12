<?php namespace App\Http\Controllers\Menu;

use App\Model\ModifierItem;
use \Request;
use \App\Http\Controllers\Controller;
use \App\Service\Utility;
use \App\Model\ModifierGroup;
use \App\Http\Controllers\SplickitApiCurlController;
use \App\Http\Controllers\Menu\ItemsModifiersController;
use Illuminate\Support\Facades\Log;

class ModifierController extends SplickitApiCurlController {

    //Load Sections and Mods to the UI
    public function index() {
        $menu_id = session('current_menu_id');

        $utility = new Utility();
        $lookups = ['modifier_type'];
        $lookup_values = $utility->getLookupValues($lookups);

        //Make Curl Request to API for Menu Types
        $modifiers = [];

        return ['lookup'=>$lookup_values, 'modifiers'=>$modifiers];
    }

    public function createModifierGroup() {
        $menu_id = session('current_menu_id');

        $post = Request::all();
        $this->data = $post['new_modifier_group'];

        if (substr(trim($this->data['item_list']), -1) == ';') {
            $this->data['item_list'] = rtrim($this->data['item_list'],';');
        }

        $utility = new Utility();

        $this->data['active'] = $utility->convertBooleanYN($this->data['active']);

        $this->api_endpoint = 'menus/'.$menu_id.'/modifier_groups';

        //Audit Data
        $this->audit_data['action'] = 'Create';
        $this->audit_data['auditable_type'] = 'Menu_Modifier-Group';
        $this->audit_data['response_auditable_id'] = 'modifier_group_id';

        $response = $this->makeCurlRequest(true);

        $item_controller = new ItemsModifiersController();

        $full_menu = $item_controller->index();
        $response['updated_mod_groups'] = $full_menu['menu_response']['menu']['modifier_groups'];

        foreach ($post['modifier_groups'] as $modifier_group) {
            if (isset($modifier_group['opened'])) {
                if ($modifier_group['opened']) {
                    foreach ($response['updated_mod_groups'] as $index =>$updated_mod_group) {
                        if ($modifier_group['modifier_group_id'] == $updated_mod_group['modifier_group_id']) {
                            $response['updated_mod_groups'][$index]['opened'] = true;
                        }
                    }
                }
            }
        }

        return $response;
    }

    public function updateModifierGroup() {
        $menu_id = session('current_menu_id');

        $this->data = Request::all();

        $utility = new Utility();

        $this->data['active'] = $utility->convertBooleanYN($this->data['active']);

        $this->api_endpoint = 'menus/'.$menu_id.'/modifier_groups/'.$this->data['modifier_group_id']."?merchant_id=0";

        //Audit Data
        $this->audit_data['auditable_id'] = $this->data['modifier_group_id'];
        $this->audit_data['action'] = 'Update';
        $this->audit_data['auditable_type'] = 'Menu_Modifier-Group';

        $response = $this->makeCurlRequest(true);

        return $response;
    }

    public function currentModifierGroups() {
        return session('current_modifier_groups');
    }

    public function reOrderModifierGroups() {
        $this->data = Request::all();

        $current_priority = count($this->data['modifier_groups']) * 1000;

        foreach ($this->data['modifier_groups'] as $modifier_group) {
            $menu_type = ModifierGroup::find($modifier_group['modifier_group_id']);

            $menu_type->priority = $current_priority;

            $menu_type->save();

            $modifier_items = ModifierItem::where('modifier_group_id', '=', $modifier_group['modifier_group_id'])->orderBy('priority')->get();

            $mod_item_count = 1;

            foreach ($modifier_items as $modifier_item) {
                $mod_item_priority = $current_priority + $mod_item_count;

                $current_mod_item = ModifierItem::find($modifier_item->modifier_item_id);

                $current_mod_item->priority = $mod_item_priority;

                $current_mod_item->save();

                $mod_item_count++;
            }
            $current_priority = $current_priority - 1000;
        }
        return 1;
    }

    public function deleteModifierGroup($modifier_group_id) {
        $menu_id = session('current_menu_id');

        $this->api_endpoint = 'menus/'.$menu_id.'/modifier_groups/'.$modifier_group_id;

        $this->setMethodDelete();

        $response = $this->makeCurlRequest();

        return $response;
    }
}



