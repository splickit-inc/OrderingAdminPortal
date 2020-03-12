<?php namespace App\Http\Controllers\Menu;

use App\Http\Controllers\SplickitApiCurlController;
use App\Model\MenuItem;
use App\Model\MenuType;
use App\Model\ModifierGroup;
use App\Model\ModifierItem;
use App\Model\Photo;
use App\Model\PortalMerchantGroups;
use App\Service\Utility;
use Auth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Request;
use Intervention\Image\ImageManagerStatic as Image;


class ItemsModifiersController extends SplickitApiCurlController
{

    //Load Sections and Mods to the UI
    public function index()
    {
        $menu_id = session('current_menu_id');

        $utility = new Utility();
        $lookups = ['cat_id'];
        $lookup_values = $utility->getLookupValues($lookups);

        $this->data = [];

        if (session('user_visibility') == 'operator') {
            $merchant_id = session('operator_merchant_id');
        } else {
            $merchant_id = 0;
        }

        $this->api_endpoint = 'menus/' . $menu_id . "?merchant_id=" . $merchant_id;

        $menu_response = $this->makeCurlRequest();

        Log::info('menu response' . json_encode($menu_response));

        session(['current_menu_types' => $menu_response['menu']['menu_types']]);
        session(['current_modifier_groups' => $menu_response['menu']['modifier_groups']]);

        $all_menu_items = [];
        $all_mod_items = [];

        //Build All Items and All Mod Items for Search/Filter
        foreach ($menu_response['menu']['menu_types'] as $menu_type_index => $menu_type) {

            if ($menu_type['start_time'] == '00:00:00' && $menu_type['end_time'] == '23:59:59') {
                $menu_response['menu']['menu_types'][$menu_type_index]['active_all_day'] = true;
            } else {
                $menu_response['menu']['menu_types'][$menu_type_index]['active_all_day'] = false;
            }

            $menu_response['menu']['menu_types'][$menu_type_index]['start_time'] = date('h:i', strtotime($menu_type['start_time']));
            $menu_response['menu']['menu_types'][$menu_type_index]['start_time_am_pm'] = date('a', strtotime($menu_type['start_time']));

            $menu_response['menu']['menu_types'][$menu_type_index]['end_time'] = date('h:i', strtotime($menu_type['end_time']));
            $menu_response['menu']['menu_types'][$menu_type_index]['end_time_am_pm'] = date('a', strtotime($menu_type['end_time']));

            foreach ($menu_type['menu_items'] as $menu_item) {
                $menu_item['section'] = $menu_type;
                $all_menu_items[] = $menu_item;
            }
            foreach ($menu_type['sizes'] as $size_index => $size) {
                $menu_response['menu']['menu_types'][$menu_type_index]['sizes'][$size_index]['active'] = $utility->convertYNBoolean($size['active']);
                $menu_response['menu']['menu_types'][$menu_type_index]['sizes'][$size_index]['default_selection'] = $utility->converOneZeroNBoolean($size['default_selection']);
            }
            $menu_response['menu']['menu_types'][$menu_type_index]['active'] = $utility->convertYNBoolean($menu_type['active']);
        }

        foreach ($menu_response['menu']['modifier_groups'] as $modifier_index => $modifier_group) {
            foreach ($modifier_group['modifier_items'] as $modifier_item) {
                $modifier_item['modifier_group'] = $modifier_group;
                $all_mod_items[] = $modifier_item;
            }
            $menu_response['menu']['modifier_groups'][$modifier_index]['active'] = $utility->convertYNBoolean($modifier_group['active']);
        }

        return ['menu_response' => $menu_response, 'lookup' => $lookup_values, 'all_menu_items' => $all_menu_items, 'all_mod_items' => $all_mod_items];
    }

    public function getModifierItem()
    {
        $menu_id = session('current_menu_id');

        $modifier_group_item = session('current_modifier_item_id');
        $modifier_group_id = session('current_modifier_item_group_id');

        $utility = new Utility();

        $this->data = [];

        if ($modifier_group_item == 'new') {
            $this->api_endpoint = 'menus/' . $menu_id . '/modifier_groups/' . $modifier_group_id . '/modifier_items/new?merchant_id=0';
        } else {
            $this->api_endpoint = 'menus/' . $menu_id . '/modifier_groups/' . $modifier_group_id . '/modifier_items/' . $modifier_group_item . '?merchant_id=0';
        }

        $this->setMethodGet();
        $response = $this->makeCurlRequest();

        foreach ($response['all_sizes'] as $index => $size) {
            $response['all_sizes'][$index]['has_price'] = false;
            $response['all_sizes'][$index]['modifier_price'] = 0;
            $response['all_sizes'][$index]['active'] = false;
        }

        $response['default_modifier_price'] = [];
        $response['active_sizes'] = [];

        foreach ($response['mod_size_prices'] as $index => $mod_size_price) {
            if ($index != 0) {
                foreach ($response['all_sizes'] as $size_index => $size) {
                    if ($index == $response['all_sizes'][$size_index]['size_id']) {
                        $response['all_sizes'][$size_index]['has_price'] = true;
                        $response['all_sizes'][$size_index]['active'] = $utility->convertYNBoolean($mod_size_price['active']);
                        $response['all_sizes'][$size_index]['modifier_size_id'] = $mod_size_price['modifier_size_id'];

                        $response['all_sizes'][$size_index]['modifier_price'] = $mod_size_price['modifier_price'];
                        $response['all_sizes'][$size_index]['external_id'] = $mod_size_price['external_id'];

                        $response['active_sizes'][] = $response['all_sizes'][$size_index]['size_id'];
                    }
                }
            } else {
                $response['default_modifier_price']['size_name'] = $utility->findArrayPropertyValueWithProperty($response['all_sizes'], 'size_id', $mod_size_price['modifier_size_id'], 'size_name');
                $response['default_modifier_price']['price'] = round($mod_size_price['modifier_price'], 2);
                $response['default_modifier_price']['active'] = $utility->convertYNBoolean($mod_size_price['active']);
                $response['default_modifier_price']['external_id'] = $mod_size_price['external_id'];
            }
        }

        if (!isset($response['default_modifier_price']['price'])) {
            $response['default_modifier_price']['price'] = 0;
        }
        return $response;
    }

    public function setMenuItem($menu_type_id, $item_id, $mod_group_item_id)
    {
        session(['current_item_menu_type_id' => $menu_type_id]);
        session(['current_item_id' => $item_id]);
        session(['new_item_mod_group_config_item_id' => $mod_group_item_id]);
        return 1;
    }

    public function getItem()
    {
        $utility = new Utility();
        $menu_id = session('current_menu_id');
        $menu_type_id = session('current_item_menu_type_id');
        $item_id = session('current_item_id');
        $mod_group_item_id = session('new_item_mod_group_config_item_id');

        $this->data = [];

        if ($item_id == 'new') {
            if ($mod_group_item_id) {
                $this->data['copy_imgm_from_item_id'] = $mod_group_item_id;
            }
            $this->api_endpoint = 'menus/' . $menu_id . '/menu_types/' . $menu_type_id . '/menu_items';
        }
        else {
            $this->api_endpoint = 'menus/' . $menu_id . '/menu_types/' . $menu_type_id . '/menu_items/' . $item_id . '?merchant_id=0';
        }

        $this->setMethodGet();
        $response = $this->makeCurlRequest();

        Log::alert('SMAW API SENT ITEM SIZES' . json_encode($response['sizes']));
        Log::alert('SMAW API SENT ITEM SIZES' . json_encode($response['size_prices']));

        foreach ($response['sizes'] as $index => $menu_size) {
            $response['sizes'][$index]['include'] = true;
            if (isset($response['size_prices'][$menu_size['size_id']]['price'])) {
                $response['sizes'][$index]['price'] = $response['size_prices'][$menu_size['size_id']]['price'];
                $response['sizes'][$index]['active'] = $response['size_prices'][$menu_size['size_id']]['active'];
                $response['sizes'][$index]['external_size_id'] = $response['size_prices'][$menu_size['size_id']]['external_id'];
                $response['sizes'][$index]['priority'] = $response['size_prices'][$menu_size['size_id']]['priority'];
            } else {
                $response['sizes'][$index]['price'] = 0;
                $response['sizes'][$index]['active'] = 'N';
            }
        }

        Log::alert('PORTAL PREPARED UI SIZES' . json_encode($response['sizes']));

        foreach ($response['modifier_groups'] as $index => $modifier_group) {
            if (isset($response['allowed_modifier_groups'][$modifier_group['modifier_group_id']])) {
                $response['modifier_groups'][$index]['allowed'] = 1;
                $response['modifier_groups'][$index]['min'] = $response['allowed_modifier_groups'][$modifier_group['modifier_group_id']]['min'];
                $response['modifier_groups'][$index]['display_name'] = $response['allowed_modifier_groups'][$modifier_group['modifier_group_id']]['display_name'];
                $response['modifier_groups'][$index]['max'] = $response['allowed_modifier_groups'][$modifier_group['modifier_group_id']]['max'];
                $response['modifier_groups'][$index]['price_override'] = $response['allowed_modifier_groups'][$modifier_group['modifier_group_id']]['price_override'];
                $response['modifier_groups'][$index]['price_max'] = $response['allowed_modifier_groups'][$modifier_group['modifier_group_id']]['price_max'];
                $response['modifier_groups'][$index]['priority'] = $response['allowed_modifier_groups'][$modifier_group['modifier_group_id']]['priority'];
            } else {
                $response['modifier_groups'][$index]['allowed'] = 0;
            }
        }

        $response['allowed_modifier_group_map_ids'] = [];

        if (isset($response['allowed_modifier_groups'])) {
            foreach ($response['allowed_modifier_groups'] as $group_index => $modifier_group) {
                $response['allowed_modifier_group_map_ids'][$modifier_group['modifier_group_id']] = $modifier_group['map_id'];

                $response['allowed_modifier_groups'][$modifier_group['modifier_group_id']]['modifier_group_name'] =
                    $utility->findArrayPropertyValueWithProperty($response['modifier_groups'], 'modifier_group_id', $modifier_group['modifier_group_id'], 'modifier_group_name');

                if (isset($modifier_group['modifier_items'])) {
                    foreach ($modifier_group['modifier_items'] as $item_index => $modifier_item) {
                        if (isset($response['comes_with_modifier_items'][$modifier_item['modifier_item_id']])) {
                            $response['allowed_modifier_groups'][$group_index]['modifier_items'][$item_index]['comes_with'] = true;
                        } else {
                            $response['allowed_modifier_groups'][$group_index]['modifier_items'][$item_index]['comes_with'] = false;
                        }
                    }
                } else {
                    $response['allowed_modifier_groups'][$group_index]['modifier_items'] = [];
                }
            }
        } else {
            $response['allowed_modifier_groups'] = [];
        }

        if (!isset($response['comes_with_modifier_items'])) {
            $response['comes_with_modifier_items'] = [];
        }

        if ($item_id == 'new') {
            $photo = [
                'exist' => false,
                'url' => ''
            ];

        } else {
            $photo = [
                'exist' => false,
            ];

            //See if Photo Uploaded
            try {
                $item_photo = Photo::where('item_id', '=', $item_id)->where('width', '=', '79')->where('height', '=', '79')->firstOrFail();
                $response_url_small_x1 = $item_photo->url;

                $photo['url_thumbnail'] = $response_url_small_x1 . "?" . time();
                $photo['exist'] = true;

            } catch (\Exception $exception) {
                $photo['url_thumbnail'] = '';
            }
            try {
                $item_photo = Photo::where('item_id', '=', $item_id)->where('width', '=', '320')->where('height', '=', '210')->firstOrFail();
                $response_url_large_x2 = $item_photo->url;
                $photo['url_main'] = $response_url_large_x2 . "?" . time();
                $photo['exist'] = true;
            } catch (\Exception $e) {
                $photo['url_main'] = '';
            }
        }

        $response['image'] = $photo;
        $response['comes_with_mod_items_to_be_removed'] = $photo;

        if (!isset($response['item_name'])) {
            $response['item_name'] = '';
        }

        $menu_type = MenuType::find($response['menu_type_id']);
        $response['menu_type_name'] = $menu_type->menu_type_name;

        return $response;
    }

    public function fullItem($item_id)
    {
        $item = MenuItem::find($item_id);

        session(['current_item_id' => $item_id]);
        session(['current_item_menu_type_id' => $item->menu_type_id]);

        return $this->getItem();
    }

    public function buildZeroData($array)
    {
        $index = 0;
        foreach ($array as $item) {
            foreach ($item as $item_property => $value) {
                $this->data[$index . $item_property] = $value;
            }
            $index++;
        }
    }

    public function updateItem()
    {
        $this->data = Request::all();
        $menu_id = session('current_menu_id');

        $utility = new Utility();

        //Convert Active to Y/N
        $this->data['active'] = $utility->convertBooleanYN($this->data['active']);

        if (!isset($this->data['external_item_id'])) {
            $this->data['external_item_id'] = '';
        }

        //Sizes
        $index = 0;
        Log::alert('PORTAL UI SUBMITTED SIZE PRICES' . json_encode($this->data['size_prices']));

        foreach ($this->data['sizes'] as $size_index => $size) {
            if (!isset($this->data['size_prices'][$size['size_id']])) {
                $price = (float)$size['price'];
                if ($price > 0 || $size['active'] || strlen($size['external_size_id'] > 2)) {
                    $this->data['size_prices'][$size['size_id']] = [
                        'item_size_id' => null,
                        'size_id' => $size['size_id'],
                        'active' => $utility->convertBooleanYN($size['active']),
                        'price' => $size['price']
                    ];
                }
            }
        }

        foreach ($this->data['size_prices'] as $id => $size_price) {
            if ($id != 0) {
                $this->data[$index . 'sizeprice_id'] = $size_price['item_size_id'];
                $this->data[$index . 'size_id'] = $id;
                $this->data[$index . 'active'] = $utility->convertBooleanYN($utility->findArrayPropertyValueWithProperty($this->data['sizes'], 'size_id', $id, 'active'));
                $this->data[$index . 'price'] = $utility->findArrayPropertyValueWithProperty($this->data['sizes'], 'size_id', $id, 'price');
                $this->data[$index . 'external_id'] = $utility->findArrayPropertyValueWithProperty($this->data['sizes'], 'size_id', $id, 'external_size_id');
                //$this->data[$index . 'external_size_id'] = $utility->findArrayPropertyValueWithProperty($this->data['sizes'], 'size_id', $id, 'external_size_id');
                $this->data[$index . 'priority'] = $utility->findArrayPropertyValueWithProperty($this->data['sizes'], 'size_id', $id, 'priority');
                $this->data[$index . 'itemsize_priority'] = $utility->findArrayPropertyValueWithProperty($this->data['sizes'], 'size_id', $id, 'priority');
                $this->data[$index . 'priority'] = $utility->findArrayPropertyValueWithProperty($this->data['sizes'], 'size_id', $id, 'priority');

                if ($this->data['new']) {
                    $this->data[$index . 'include'] = $utility->findArrayPropertyValueWithProperty($this->data['sizes'], 'size_id', $id, 'include');
                }

                $index++;
            }
        }

        Log::alert('PORTAL UI READY FOR POST DATA' . json_encode($this->data));

        //Build Modifier Groups in Smaw Structure
        $index = 0;
        foreach ($this->data['modifier_groups'] as $modifier_group) {

            $this->data[$index . "modifier_group_id"] = $modifier_group['modifier_group_id'];

            if (isset($this->data['allowed_modifier_groups'][$modifier_group['modifier_group_id']])) {
                $this->data[$index . "allowed"] = "ON";
            } else {
                $this->data[$index . "allowed"] = "OFF";
            }

            $this->data[$index . "display_name"] = $modifier_group['modifier_group_name'];

            if (isset($modifier_group['min'])) {
                $this->data[$index . "min"] = $modifier_group['min'];
            } else {
                $this->data[$index . "min"] = "";
            }

            if (isset($modifier_group['max'])) {
                $this->data[$index . "max"] = $modifier_group['max'];
            } else {
                $this->data[$index . "max"] = "";
            }

            if (isset($modifier_group['price_override'])) {
                $this->data[$index . "price_override"] = (string)$modifier_group['price_override'];
            } else {
                $this->data[$index . "price_override"] = (float)0;
            }

            if (isset($modifier_group['price_max'])) {
                $this->data[$index . "price_max"] = $modifier_group['price_max'];
            } else {
                $this->data[$index . "price_max"] = "";
            }

            if (isset($modifier_group['push_this_mapping_to_each_item_in_menu_type'])) {
                if ($modifier_group['push_this_mapping_to_each_item_in_menu_type']) {
                    $this->data[$index . "push_this_mapping_to_each_item_in_menu_type"] = 1;
                } else {
                    $this->data[$index . "push_this_mapping_to_each_item_in_menu_type"] = 0;
                }
            } else {
                $this->data[$index . "push_this_mapping_to_each_item_in_menu_type"] = 0;
            }

            if (isset($modifier_group['display_name'])) {
                $this->data[$index . "display_name"] = $modifier_group['display_name'];
            } else {
                $this->data[$index . "display_name"] = "";
            }

            $this->data[$index . "priority"] = $modifier_group['priority'];

            if (isset($this->data['allowed_modifier_group_map_ids'][$modifier_group['modifier_group_id']])) {
                $this->data[$index . "item_modifier_group_map_id"] = $this->data['allowed_modifier_group_map_ids'][$modifier_group['modifier_group_id']];
            } else {
                $this->data[$index . "item_modifier_group_map_id"] = null;
            }

            $index++;
        }

        //Convert Sizes to 'Y/N' Rather than True False Boolean
        foreach ($this->data['sizes'] as $index => $size) {
            $this->data['sizes'][$index]['active'] = $utility->convertBooleanYN($size['active']);
        }

        $allowed_mod_items = [];
        //Build Allowed Modifier Groups in Smaw Data Structure
        $comes_with_index = 0;
        foreach ($this->data['allowed_modifier_groups'] as $allowed_modifier_group) {
            $current_allowed_mod_items = array_map(function($mod_item) {
                return $mod_item['modifier_item_id'];
            }, $allowed_modifier_group['modifier_items']);

            $allowed_mod_items = array_merge($current_allowed_mod_items, $allowed_mod_items);

            foreach ($allowed_modifier_group['modifier_items'] as $modifier_item) {

                if (isset($this->data['comes_with_modifier_items'][$modifier_item['modifier_item_id']])) {
                    $this->data[$comes_with_index . "item_modifier_item_map_id"] = $this->data['comes_with_modifier_items'][$modifier_item['modifier_item_id']]['map_id'];
                } else {
                    $this->data[$comes_with_index . "item_modifier_item_map_id"] = null;
                }

                $this->data[$comes_with_index . "modifier_item_id"] = $modifier_item['modifier_item_id'];

                if (isset($modifier_item['comes_with'])) {
                    $this->data[$comes_with_index . "comes_with"] = ($modifier_item['comes_with']) ? 'ON' : 'OFF';
                } else {
                    $this->data[$comes_with_index . "comes_with"] = 'OFF';
                }
                $comes_with_index++;
            }
        }
        //If a modifier group is no longer allowed, then its modifier items can no longer "come with" by default
        foreach ($this->data['comes_with_modifier_items'] as $previous_comes_with_mod_item) {
            if (!in_array($previous_comes_with_mod_item['modifier_item_id'], $allowed_mod_items)) {
                $this->data[$comes_with_index . "item_modifier_item_map_id"] = $previous_comes_with_mod_item['map_id'];
                $this->data[$comes_with_index . "modifier_item_id"] = $previous_comes_with_mod_item['modifier_item_id'];
                $this->data[$comes_with_index . "comes_with"] = 'OFF';
                $comes_with_index++;
            }
        }


        $this->data['number_of_possible_modifier_items'] = $comes_with_index;

        //$this->buildZeroData($this->data['sizes']);

        Log::alert('PORTAL UI READY FOR POST DATA AFTER BUILD ZERO' . json_encode($this->data));

        if (isset($this->data['item_id'])) {
            $this->api_endpoint = 'menus/' . $menu_id . '/menu_types/' . $this->data['menu_type_id'] . '/menu_items/' . $this->data['item_id'];

            //Audit Data
            $this->audit_data['auditable_id'] = $this->data['item_id'];
            $this->audit_data['action'] = 'Update';
        } else {
            $this->api_endpoint = 'menus/' . $menu_id . '/menu_types/' . $this->data['menu_type_id'] . '/menu_items?merchant_id=0';

            //Audit Data
            $this->audit_data['response_auditable_id'] = 'item_id';
            $this->audit_data['action'] = 'Create';
        }

        if (isset($this->data['propagate_type'])) {
            if ($this->data['propagate_type'] == 'subset') {
                $this->data['propogate_to_merchant_ids'] = implode(',', array_column($this->data['propagate_merchants'], 'merchant_id'));
            } elseif ($this->data['propagate_type'] == 'All') {
                $this->data['propogate_to_merchant_ids'] = 'all';
            } elseif ($this->data['propagate_type'] == 'group') {
                $merchant_group = new PortalMerchantGroups();

                $this->data['propogate_to_merchant_ids'] = $merchant_group->getMerchantIds($this->data['merchant_group_id']);
            } else {
                $this->data['propogate_to_merchant_ids'] = $this->data['propagate_type'];
            }
        }
        $this->audit_data['auditable_type'] = 'Menu_Item';

        $this->setMethodPost();
        $response = $this->makeCurlRequest(true);

        if (!isset($response['error'])) {
            return response()->json($response, 200);
        }
        else {
            return response()->json($response, 404);
        }
    }

    public function setModifierItem($mod_group_id, $mod_item_id)
    {
        session(['current_modifier_item_id' => $mod_item_id]);
        session(['current_modifier_item_group_id' => $mod_group_id]);
        return 1;
    }

    public function updateModifierItem()
    {
        $this->data = Request::all();
        $menu_id = session('current_menu_id');
        $utility = new Utility();

        $this->data['merchant_id'] = 0;
        $this->data['update_child_records'] = 'yes';
        $this->data["apply_prices_to_all_modifier_items_in_group"] = 'no';

        $this->data["0size_id"] = "0";

        if (isset($this->data['mod_size_prices'][0]['modifier_size_id'])) {
            $this->data["0mod_sizeprice_id"] = $this->data['mod_size_prices'][0]['modifier_size_id'];
        } else {
            $this->data["0mod_sizeprice_id"] = null;
        }

        if (!isset($this->data['default_modifier_price']['active'])) {
            $this->data["0active"] = 'N';
        } else {
            $this->data["0active"] = $utility->convertBooleanYN($this->data['default_modifier_price']['active']);
        }

        $this->data["0modifier_price"] = $this->data['default_modifier_price']['price'];

        if (isset($this->data['default_modifier_price']['external_id'])) {
            $this->data["0external_id"] = $this->data['default_modifier_price']['external_id'];
        } else {
            $this->data["0external_id"] = '';
        }

        $size_index = 1;

        foreach ($this->data['all_sizes'] as $size) {
            if ($size['active']) {
                $this->data[$size_index . "size_id"] = $size['size_id'];
                $this->data[$size_index . "active"] = $utility->convertBooleanYN($size['active']);
                $this->data[$size_index . "has_price"] = $utility->convertBooleanYN($size['has_price']);
                $this->data[$size_index . "modifier_price"] = $size['modifier_price'];

                if (isset($size['external_id'])) {
                    $this->data[$size_index . "external_id"] = $size['external_id'];
                } else {
                    $this->data[$size_index . "external_id"] = null;
                }


                if (isset($size['modifier_size_id'])) {
                    $this->data[$size_index . "mod_sizeprice_id"] = $size['modifier_size_id'];
                } else {
                    $this->data[$size_index . "mod_sizeprice_id"] = null;
                }
                $size_index++;
            }
            else {
                if (in_array($size['size_id'], $this->data['active_sizes'])) {
                    $this->data[$size_index . "size_id"] = $size['size_id'];
                    $this->data[$size_index . "active"] = 'N';
                    $this->data[$size_index . "has_price"] = $utility->convertBooleanYN($size['has_price']);
                    $this->data[$size_index . "modifier_price"] = $size['modifier_price'];

                    if (isset($size['external_id'])) {
                        $this->data[$size_index . "external_id"] = $size['external_id'];
                    } else {
                        $this->data[$size_index . "external_id"] = null;
                    }


                    if (isset($size['modifier_size_id'])) {
                        $this->data[$size_index . "mod_sizeprice_id"] = $size['modifier_size_id'];
                    } else {
                        $this->data[$size_index . "mod_sizeprice_id"] = null;
                    }
                    $size_index++;
                }
            }
        }

        if (isset($this->data['modifier_item_id'])) {
            $this->api_endpoint = 'menus/' . $menu_id . '/modifier_groups/' . $this->data['modifier_group_id'] . '/modifier_items/' . $this->data['modifier_item_id'] . '?merchant_id=0';

            //Audit Data
            $this->audit_data['auditable_id'] = $this->data['modifier_item_id'];
            $this->audit_data['action'] = 'Update';
        } else {
            $this->api_endpoint = 'menus/' . $menu_id . '/modifier_groups/' . $this->data['modifier_group_id'] . '/modifier_items' . '?merchant_id=0';

            //Audit Data
            $this->audit_data['response_auditable_id'] = 'modifier_item_id';
            $this->audit_data['action'] = 'Create';
        }

        if (isset($this->data['propagate_type'])) {
            if ($this->data['propagate_type'] == 'subset') {
                $this->data['propogate_to_merchant_ids'] = implode(',', array_column($this->data['propagate_merchants'], 'merchant_id'));
            } elseif ($this->data['propagate_type'] == 'All') {
                $this->data['propogate_to_merchant_ids'] = 'all';
            } elseif ($this->data['propagate_type'] == 'group') {
                $merchant_group = new PortalMerchantGroups();

                $this->data['propogate_to_merchant_ids'] = $merchant_group->getMerchantIds($this->data['merchant_group_id']);
            } else {
                $this->data['propogate_to_merchant_ids'] = $this->data['propagate_type'];
            }
        }

        $this->audit_data['auditable_type'] = 'Menu_Modifier-Item';

        $this->setMethodPost();
        $response = $this->makeCurlRequest(true);

        return $response;
    }

    public function updateFullMenuItem()
    {
        $this->data = Request::all();
        $menu_id = session('current_menu_id');

        if ($this->data['active']) {
            $this->data['active'] = 'Y';
        } else {
            $this->data['active'] = 'N';
        }

        if (session('user_visibility') == 'operator') {
            $merchant_id = session('operator_merchant_id');
        } elseif (null !== session('merchant_menu_merchant_id')) {
            $merchant_id = session('merchant_menu_merchant_id');
        } else {
            $merchant_id = 0;
        }

        $this->api_endpoint = 'menus/' . $menu_id . '/item_price?merchant_id=' . $merchant_id;

        return $this->makeCurlRequest();
    }

    public function updateFullMenuModifierItem()
    {
        $this->data = Request::all();
        $menu_id = session('current_menu_id');

        if ($this->data['active']) {
            $this->data['active'] = 'Y';
        } else {
            $this->data['active'] = 'N';
        }

        if (session('user_visibility') == 'operator') {
            $merchant_id = session('operator_merchant_id');
        } elseif (null !== session('merchant_menu_merchant_id')) {
            $merchant_id = session('merchant_menu_merchant_id');
        } else {
            $merchant_id = 0;
        }

        $this->api_endpoint = 'menus/' . $menu_id . '/modifier_price?merchant_id=' . $merchant_id;

        $response = $this->makeCurlRequest();

        return $response;
    }

    public function uploadImage()
    {
        $this->data = Request::all();

        $user = Auth::user();

        $file = $_FILES["key"]["tmp_name"];

        $utility = new Utility();

        $extension = $utility->getImageExtension($_FILES["key"]['name']);

        $file_name = $user->id . "-" . session('current_item_id') . "." . $extension;

        $tmp_file_location = public_path() . "/img/item_tmp/" . $file_name;

        move_uploaded_file($file, $tmp_file_location);

        session(['current_item_main_file' => $tmp_file_location]);

        return 1;
    }


    public function upLoadItemImagesS3()
    {
        try {
            $data = Request::all();

            if (!empty($data['thumb']) && !empty($data['file'])) {
                /** @var UploadedFile $mainFile */
                $mainFile = $data['file'];
                /** @var UploadedFile $thumbFile */
                $thumbFile = $data['thumb'];
                $user = Auth::user();
                $base_file_name = session('current_item_id');

                //Configuration
                $large_2x_width = 420;//640;
                $large_2x_height = 276;//420;

                $large_1x_width = 320;
                $large_1x_height = 210;

                $small_2x_width = 158;
                $small_2x_height = 158;

                $small_1x_width = 79;
                $small_1x_height = 79;

                //Main Image
                $extension = "jpg";

                $large_2X = Image::make($mainFile->getRealPath())->resize($large_2x_width, $large_2x_height)->encode($extension,100);
                $large_1X = Image::make($mainFile->getRealPath())->resize($large_1x_width, $large_1x_height)->encode($extension,100);


                $large_2x_file_name = $base_file_name . '.' . $extension;
                $large_1x_file_name = $base_file_name . '.' . $extension;

                //Thumb Small Image

                $small_2X = Image::make($thumbFile->getRealPath())->resize($small_2x_width, $small_2x_height)->encode($extension,100);;
                $small_1X = Image::make($thumbFile->getRealPath())->resize($small_1x_width, $small_1x_height)->encode($extension,100);;

                $small_2x_file_name = $base_file_name . '.' . $extension;
                $small_1x_file_name = $base_file_name . '.' . $extension;

                $large_2x_file_name = 'com.yourbiz.images/large/2x/' . $large_2x_file_name;
                $large_1x_file_name = 'com.yourbiz.images/large/1x/' . $large_1x_file_name;
                $small_2x_file_name = 'com.yourbiz.images/small/2x/' . $small_2x_file_name;
                $small_1x_file_name = 'com.yourbiz.images/small/1x/' . $small_1x_file_name;

                Storage::disk('s3')->put($large_2x_file_name, (string)$large_2X->encode(), 'public');
                Storage::disk('s3')->put($large_1x_file_name, (string)$large_1X->encode(), 'public');

                Storage::disk('s3')->put($small_2x_file_name, (string)$small_2X->encode(), 'public');
                Storage::disk('s3')->put($small_1x_file_name, (string)$small_1X->encode(), 'public');

                $photo = Photo::firstOrNew(['item_id' => session('current_item_id'), 'width' => $large_2x_width, 'height' => $large_2x_height]);
                $photo->url = Storage::disk('s3')->url($large_2x_file_name);
                $photo->save();
                $response_url_large_x2 = $photo->url;

                $photo = Photo::firstOrNew(['item_id' => session('current_item_id'), 'width' => $large_1x_width, 'height' => $large_1x_height]);
                $photo->url = Storage::disk('s3')->url($large_1x_file_name);
                $photo->save();

                $photo = Photo::firstOrNew(['item_id' => session('current_item_id'), 'width' => $small_2x_width, 'height' => $small_2x_height]);
                $photo->url = Storage::disk('s3')->url($small_2x_file_name);
                $photo->save();

                $photo = Photo::firstOrNew(['item_id' => session('current_item_id'), 'width' => $small_1x_width, 'height' => $small_1x_height]);
                $photo->url = Storage::disk('s3')->url($small_1x_file_name);
                $photo->save();
                $response_url_small_x1 = $photo->url;

                return [
                    'exist' => true,
                    'url_thumbnail' => $response_url_small_x1 . "?" . time(),
                    'url_main' => $response_url_large_x2 . "?" . time(),
                ];
            }
            return response()->json(['error' => "The request must have the files content."], 404);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 404);
        }
    }

    public function reOrderItems()
    {
        $this->data = Request::all();

        $menu_type = MenuType::find($this->data['menu_type_id']);
        $item_priority = 1;

        $menu_items_reversed = array_reverse($this->data['menu_items']);

        foreach ($menu_items_reversed as $menu_item) {
            $current_item = MenuItem::find($menu_item['item_id']);

            $current_item->priority = $menu_type->priority + $item_priority;

            $current_item->save();

            $item_priority++;
        }
        return 1;
    }

    public function reOrderModifierItems()
    {
        $this->data = Request::all();

        $modifier_group = ModifierGroup::find($this->data['modifier_group_id']);

        $modifier_items_reversed = array_reverse($this->data['modifier_items']);
        $mod_item_count = 1;

        foreach ($modifier_items_reversed as $modifier_item) {
            $current_item = ModifierItem::find($modifier_item['modifier_item_id']);

            $current_item->priority = $modifier_group->priority + $mod_item_count;

            $current_item->save();

            $mod_item_count++;
        }
        return 1;
    }

    public function deleteItem($item_id)
    {
        $menu_id = session('current_menu_id');

        $this->api_endpoint = 'menus/' . $menu_id . '/menu_items/' . $item_id;

        $this->setMethodDelete();

        $response = $this->makeCurlRequest();

        return $response;
    }

    public function deleteModifierItem($modifier_item_id)
    {
        $menu_id = session('current_menu_id');

        $this->api_endpoint = 'menus/' . $menu_id . '/modifier_items/' . $modifier_item_id;

        $this->setMethodDelete();

        $response = $this->makeCurlRequest();

        return $response;
    }

}