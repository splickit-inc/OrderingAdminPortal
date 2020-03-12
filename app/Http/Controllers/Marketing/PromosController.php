<?php namespace App\Http\Controllers\Marketing;

use App\BusinessModel\Visibility\promos\IVisibility;
use \App\Http\Controllers\SplickitApiCurlController;
use App\Model\Merchant;
use App\Model\Orders;
use App\User;

use \DB;
use \App\Model\Brand;
use \App\Model\ObjectOwnership;
use \App\Model\Promo;
use \App\Model\PromoMerchantMap;
use \App\Model\MerchantMenuMap;
use \App\Service\Utility;
use \App\Service\PromoService;
use Illuminate\Support\Facades\Auth;
use App\Model\MenuType;
use App\Model\MenuItem;
use App\Model\Operator;
use App\Model\MenuSize;
use App\Model\PortalOperatorMerchantMap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\BusinessModel\Visibility\Merchant as OperatorMerchant;

class PromosController extends SplickitApiCurlController
{

    public function index()
    {
        $brands = Brand::where('production', '=', 'Y')->where('active', '=', 'Y')->orderBy('brand_name')->get()->toArray();
        return [
            'brands' => $brands
        ];
    }

    public function getRecentlyVisited()
    {
        if (session('recently_visited_promos')) {
            return session('recently_visited_promos');
        } else {
            return [];
        }
    }

    public function getBrandMenus($brand)
    {
        $menus = DB::table('Menu')
            ->join('Merchant_Menu_Map', 'Menu.menu_id', '=', 'Merchant_Menu_Map.menu_id')
            ->join('Merchant', 'Merchant_Menu_Map.merchant_id', '=', 'Merchant.merchant_id')
            ->where('Merchant.brand_id', '=', $brand)
            ->select('Menu.menu_id', 'Menu.name')
            ->distinct()
            ->get()
            ->toArray();

        return $menus;
    }

    public function setPromoMenu($menu_id)
    {
        session(['current_menu_id' => $menu_id]);

        $menu_type = MenuType::where('cat_id', '=', 'E')->where('menu_id', '=', $menu_id)->first();

        $item = MenuItem::where('menu_type_id', '=', $menu_type->menu_type_id)->first();

        $size = MenuSize::where('menu_type_id', '=', $menu_type->menu_type_id)->first();

        return [
            'menu_type_name' => $menu_type->menu_type_name,
            'item_name' => $item->item_name,
            'size_name' => $size->size_name,
        ];
    }

    public function getCurrentPromo()
    {
        $promo = Promo::find(session('current_promo_id'))->toArray();

        $this->api_endpoint = 'promos/' . $promo['promo_id'];

        $response = $this->makeCurlRequest();

        if (isset($response['error'])) {
            return $response;
        }

        if (!isset($response['qualifying_object_name_list'])) {
            $response['qualifying_object_name_list'] = [];
        }

        if (!isset($response['promotional_object_name_list'])) {
            $response['promotional_object_name_list'] = [];
        }

        $end_date = strtotime($response['end_date']);
        $end_date = $end_date + 60;
        $response['end_date'] = date('m/d/Y', $end_date);

        $response['promo_messages_to_edit'] = [];

        $message_number = 1;

        while ($message_number < 6) {
            if (isset($response['promo_messages']['message' . $message_number])) {
                $new_message = [];
                $new_message['number'] = $message_number;
                $new_message['text'] = $response['promo_messages']['message' . $message_number];
                $response['promo_messages_to_edit'][] = $new_message;
            }
            $message_number++;
        }

        if ($response['promo_type'] == 1) {
            if ($response['max_redemptions'] != 0) {
                $response['campaign_uses'] = true;
            } else {
                $response['campaign_uses'] = false;
            }

            if ($response['percent_off'] > 0) {
                $response['discount_type'] = 'percent_off';
            } else {
                $response['discount_type'] = 'dollars_off';
            }
        }

        foreach ($response['qualifying_object_name_list'] as $index => $object) {
            if ($response['qualifying_object_name_list'][$index] == 'Entre') {
                $response['qualifying_object_name_list'][$index] = 'Entrée';
            }
        }

        $response['simple_merchant_ids'] = array_map(function($merchant) {
            return $merchant['merchant_id'];
        }, $response['promo_merchant_maps']);

        $response['simple_keywords'] = array_map(function($keyword) {
            return $keyword['promo_key_word'];
        }, $response['promo_key_words']);
        $response['simple_keywords'] = implode(',',$response['simple_keywords']);

        $response['total_cart_amounts'] = Orders::where('promo_id', '=', $response['promo_id'])
                                            ->whereIn('status', ['E', 'O'])
                                            ->sum('order_amt');

        $response['promo_merchant_maps'] = array_map(function($merchant) {
            $merchant['full_merchant'] = Merchant::find($merchant['merchant_id'])->toArray();
            return $merchant;
        }, $response['promo_merchant_maps']);

        return $response;
    }

    public function getActivePromos(Promo $model, Request $request)
    {
        try {
            $order_by = $request->order_by ? $request->order_by : 'Promo.modified';
            $order_direction = $request->exists('order_direction') && $request->order_direction == 'false' ? 'ASC' : 'DES';

            $visibility = session('user_visibility');
            $result = $model->getActivePromosPaginated($visibility, $order_by, '30', $order_direction);

            //$result->items = $this->changePromoTypeToFullText($result->items());
            return response()->json($result, 200);
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 403);
        }
    }

    public function search(Request $request, Promo $promo_model)
    {
        try {
            $data = $request->all();
            $order_by = $request->order_by ? $request->order_by : 'Promo.modified';
            $order_direction = $request->exists('order_direction') && $request->order_direction == 'false' ? 'ASC' : 'DES';

            $visibility = session('user_visibility');
            /** @var IVisibility $promos */
            $promos = App::make('promos.' . $visibility);

            $result = $promos->searchForPromos($data['search_text'], $data['active_only'], $data['promo_type'])->orderBy($order_by, $order_direction)
                                ->distinct()
                                ->paginate(30, ['Promo.promo_id', 'Promo_Key_Word_Map.promo_key_word', 'Promo.start_date', 'Promo.end_date', 'Promo.description', 'Promo.promo_type', 'Promo.active']);

            if (empty($result)) {
                return response()->json(["errors" => "Forbidden"], 403);
            }
            $promo_types = $promo_model->promo_types;
            $result->getCollection()->transform(function ($value) use ($promo_types) {
                if (!empty($promo_types[$value->promo_type])) {
                    $value->promo_type = $promo_types[$value->promo_type];
                    $value->full_description = $value->description;
                    if (strlen($value->description) > 29) {
                        $value->description = substr($value->description, 0, 30) . "...";
                    }
                }
                $value->operator_owned = 'N';

                if (session('user_visibility') == 'operator') {
                    $objectOwnership = ObjectOwnership::where('object_type', '=', 'promo')->where('object_id', '=', $value->promo_id)
                        ->first();

                    if ($objectOwnership) {
                        $user = User::find($objectOwnership->user_id);
                        if ($user->visibility == 'operator') {
                            $value->operator_owned = 'Y';
                        }
                    }
                }
                return $value;
            });

            return response()->json($result, 200);
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 403);
        }
    }

    public function addPromoToRecentlyVisited($new_promo)
    {
        $new_promo = [
            'promo_id' => $new_promo->promo_id,
            'description' => $new_promo->description
        ];

        if (session('recently_visited_promos')) {
            $recently_visited_promos = session('recently_visited_promos');

            //Remove Merchant if It's Already in Recently Visited
            foreach ($recently_visited_promos as $key => $promo) {
                if ($promo['promo_id'] == $new_promo['promo_id']) {
                    unset($recently_visited_promos[$key]);
                    break;
                }
            }

            //Remove the Oldest Recently Visited if We're at the Maximum of 5
            if (count($recently_visited_promos) == 5) {
                array_pop($recently_visited_promos);
            }

            //Add the New Menu to the Beginning of the Array
            array_unshift($recently_visited_promos, $new_promo);
        } else {
            $recently_visited_promos = [];
            $recently_visited_promos[] = $new_promo;
        }
        session(['recently_visited_promos' => $recently_visited_promos]);
    }

    public function setCurrentPromo($promo_id)
    {
        session(['current_promo_id' => $promo_id]);

        $promo = Promo::find($promo_id);

        $this->addPromoToRecentlyVisited($promo);

        return 1;
    }

    public function setMenuId($menu_id)
    {
        session(['current_menu_id' => $menu_id]);

        $menu = Menu::find($menu_id);

        $this->addMenuToRecentlyVisited($menu);

        return 1;
    }

    public function removeMerchant($merchant_id)
    {
        PromoMerchantMap::where('merchant_id', '=', $merchant_id)->where('promo_id', '=', session('current_promo_id'))->delete();
        return 1;
    }

    public function create(Request $request)
    {
        $utility = new Utility();

        $this->data = $request->all();
        $this->data['start_time'] = '00:00:00';
        $this->data['end_time'] = '00:00:00';

        $promo_service = new PromoService();
        $promo_service->data = $this->data;

        $this->api_endpoint = 'promos';

        $user = new User();

        if (!$user->checkPermission('multi_merchs_filter')) {
            $this->data['merchant_id'] = session('operator_merchant_id');

            $merchant = Merchant::find(session('operator_merchant_id'));
            $this->data['brand_id'] = $merchant->brand_id;
        }
        else {
            if (sizeof($this->data['merchants']) > 0) {
                $this->data['merchant_id'] = implode($this->data['merchants'], ',');
            } else {

                if (session('user_visibility') == 'operator') {
                    $operatorMerchantMap = new PortalOperatorMerchantMap();

                    $merchants = $operatorMerchantMap->where('user_id', '=', Auth::user()->id)->get(['merchant_id'])->toArray();
                    $this->data['merchant_id'] = implode(array_column($merchants, 'merchant_id'), ',');
                }
                else {
                    $this->data['merchant_id'] = 0;
                }
            }
        }

        if (isset($this->data['valid_on_first_order_only'])) {
            $this->data['valid_on_first_order_only'] = $utility->convertBooleanYN($this->data['valid_on_first_order_only']);
        } else {
            $this->data['valid_on_first_order_only'] = 'N';
        }

        if ($this->data['automatic_promo']) {
            $this->data['key_word'] = ''.time();
            $this->data['auto_promo'] = 1;
        }

        //Promo Type 1 for Percent/Amount Off Order
        if ($this->data['promo_type'] == 2) {
            $this->data['qualifying_object_array'] = $promo_service->buildSmawMenuEntityArray($this->data['bogo_menu_selections']['buy_selections']);
            $this->data['promo_item_1_array'] = $promo_service->buildSmawMenuEntityArray($this->data['bogo_menu_selections']['get_selections']);
        } elseif ($this->data['promo_type'] == 4) {
            $this->data['qualifying_object_array'] = $promo_service->buildSmawMenuEntityArray($this->data['buy_any_discount']);
        } elseif ($this->data['promo_type'] == 5) {
            $this->data['qualifying_object_array'] = $promo_service->buildSmawMenuEntityArray($this->data['buy_all_discount']);
        }

        $this->unsetMenuObjectsFromData();

        $response = $this->makeCurlRequest();

        $objectOwnership = new ObjectOwnership();

        $objectOwnership->user_id  = Auth::user()->id;

        $objectOwnership->object_type  = 'promo';

        $objectOwnership->organization_id  = session('user_organization_id');

        $objectOwnership->object_id  = $response['promo_id'];

        $objectOwnership->save();

        return $response;
    }

    public function unsetMenuObjectsFromData() {
        $menu_objects = ['buy_any_discount_section_items_list', 'bogo_menu_selections', 'buy_any_discount', 'buy_all_discount', 'buy_any_discount_section_items_sizes_list',
            'buy_all_discount_section_items_list', 'buy_all_discount_section_items_sizes_list', 'selected_get_section_items_sizes_list', 'buy_section_items', 'selected_buy_section_items_list',
            'get_section_items'
            ];

        foreach ($menu_objects as $menu_object) {
            if (isset($this->data[$menu_object])) {
                unset($this->data[$menu_object]);
            }
        }
    }

    public function editPromoAddMerchant(Request $request)
    {
        $data = $request->all();

        $start_date = date("m/d/Y", strtotime($data['start_date']));
        $end_date = date("m/d/Y", strtotime($data['end_date']));

        $new_promo_merchant_map = new PromoMerchantMap();

        $new_promo_merchant_map->promo_id = session('current_promo_id');

        $new_promo_merchant_map->merchant_id = $data['merchant_id'];

        $new_promo_merchant_map->start_date = $start_date;

        $new_promo_merchant_map->end_date = $end_date;

        if (!isset($data['max_discount_per_order'])) {
            $new_promo_merchant_map->max_discount_per_order = null;
        } else {
            $new_promo_merchant_map->max_discount_per_order = $data['max_discount_per_order'];
        }

        $new_promo_merchant_map->save();
    }

    public function updatePromo(Request $request)
    {
        $this->data = $request->all();
        $utility = new Utility();

        $this->api_endpoint = 'promos/' . session('current_promo_id');

        $this->audit_data['auditable_type'] = 'Promo-Update';
        $this->audit_data['auditable_id'] = session('current_promo_id');
        $this->audit_data['action'] = 'Update';

        if (isset($this->data['valid_on_first_order_only'])) {
            $this->data['valid_on_first_order_only'] = $utility->convertBooleanYN($this->data['valid_on_first_order_only']);
        } else {
            $this->data['valid_on_first_order_only'] = 'N';
        }

        $this->data['active'] = $utility->convertBooleanYN($this->data['active']);

        foreach ($this->data['promo_messages_to_edit'] as $message_to_edit) {
            $this->data['promo_messages']['message' . $message_to_edit['number']] = $message_to_edit['text'];
        }
        return $this->makeCurlRequest(true);

    }

    public function validateKeywords(Request $request)
    {
        $this->data = $request->all();

        $key_words = explode(',', trim($this->data['key_words']));

        $key_word_issues = [
            'valid_key_words' => true,
            'key_word_issues' => []
        ];

        $user = new User();

        if (!$user->checkPermission('multi_merchs_filter')) {
            $this->data['merchants'] = [session('operator_merchant_id')];
        }

        if (sizeof($this->data['merchants']) > 0) {
            foreach ($this->data['merchants'] as $merchant) {
                foreach ($key_words as $key_word) {
                    $bindings = [$key_word, $merchant, 'Y', $this->data['start_date'], $this->data['end_date'], $this->data['start_date'], $this->data['end_date']];

                    $key_word_count = DB::table('Promo_Key_Word_Map')
                        ->join('Promo_Merchant_Map', 'Promo_Key_Word_Map.promo_id', '=', 'Promo_Merchant_Map.promo_id')
                        ->join('Promo', 'Promo_Key_Word_Map.promo_id', '=', 'Promo.promo_id')
                        ->where('Promo_Key_Word_Map.promo_key_word', '=', '?')
                        ->where('Promo_Merchant_Map.merchant_id', '=', '?')
                        ->where('Promo.active', '=', '?')
                        ->whereRaw('((Promo.start_date >= ? and Promo.start_date <= ?) or (Promo.end_date >= ? and Promo.end_date <= ?))')
                        ->setBindings($bindings)
                        ->count();

                    if ($key_word_count > 0) {
                        $key_word_issues['valid_key_words'] = false;
                    }
                }
            }
        } else {
            foreach ($key_words as $key_word) {
                $bindings = [$key_word, $this->data['brand_id'], 'Y', $this->data['start_date'], $this->data['end_date'], $this->data['start_date'], $this->data['end_date']];

                //connection('reports_db')->
                $key_word_count = DB::table('Promo_Key_Word_Map')
                    ->join('Promo', 'Promo_Key_Word_Map.promo_id', '=', 'Promo.promo_id')
                    ->where('Promo_Key_Word_Map.promo_key_word', '=', '?')
                    ->where('Promo_Key_Word_Map.brand_id', '=', '?')
                    ->where('Promo.active', '=', '?')
                    ->whereRaw('((Promo.start_date >= ? and Promo.start_date <= ?) or (Promo.end_date >= ? and Promo.end_date <= ?))')
                    ->setBindings($bindings)
                    ->count();

                if ($key_word_count > 0) {
                    $key_word_issues['valid_key_words'] = false;
                }
            }
        }
        return $key_word_issues;
    }

    public function validateMerchantsMenu(Request $request)
    {
        $this->data = $request->all();

        $response = [];
        $response['valid_merchants_menu'] = true;
        $response['invalid_merchants'] = [];

        foreach ($this->data['merchants'] as $merchant) {
            $count = MerchantMenuMap::where('merchant_id', '=', $merchant)
                ->where('menu_id', '=', $this->data['menu'])
                ->where('logical_delete', '=', 'N')
                ->count();

            if ($count < 1) {
                $response['valid_merchants_menu'] = false;
            }
        }
        return $response;
    }

    public function deletePromoKeyword($keyword_map_id) {
        $this->api_endpoint = 'promos/' . session('current_promo_id').'/key_words/'.$keyword_map_id;

        $this->audit_data['auditable_type'] = 'Promo-DeleteKeyword';
        $this->audit_data['auditable_id'] = session('current_promo_id');
        $this->audit_data['action'] = 'DeleteKeyword';

        $this->setMethodDelete();

        $response = $this->makeCurlRequest(true);

        return $response;
    }

    public function deleteMerchant($merchant_map_id) {
        $this->api_endpoint = 'promos/' . session('current_promo_id').'/merchant_maps/'.$merchant_map_id;

        $this->audit_data['auditable_type'] = 'Promo-DeleteMerchant';
        $this->audit_data['auditable_id'] = session('current_promo_id');
        $this->audit_data['action'] = $merchant_map_id;

        $this->setMethodDelete();

        $response = $this->makeCurlRequest(true);

        return $response;
    }

    public function addKeyword(Request $request) {
        $this->data = $request->all();

        $this->api_endpoint = 'promos/' . session('current_promo_id').'/key_words';

        $this->audit_data['auditable_type'] = 'Promo-AddKeyword';
        $this->audit_data['auditable_id'] = session('current_promo_id');
        $this->audit_data['action'] = $this->data['promo_key_word'];

        $this->setMethodPost();

        $response = $this->makeCurlRequest(true);

        return $response;
    }

    public function addMerchant(Request $request) {
        $this->data = $request->all();

        $this->api_endpoint = 'promos/' . session('current_promo_id').'/merchant_maps';

        $this->audit_data['auditable_type'] = 'Promo-AddMerchant';
        $this->audit_data['auditable_id'] = session('current_promo_id');
        $this->audit_data['action'] = $this->data['merchant_id'];

        $this->setMethodPost();

        $response = $this->makeCurlRequest(true);

        return $response;
    }
}