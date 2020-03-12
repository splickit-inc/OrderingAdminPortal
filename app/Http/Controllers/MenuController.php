<?php namespace App\Http\Controllers;

use \Illuminate\Http\Request;
use \DB;
use \Cache;
use \App\Model\Brand;
use Illuminate\Support\Facades\Log;
use \App\Http\Controllers\SplickitApiCurlController;
use App\Model\Menu;
use App\Model\Merchant;
use App\Model\MerchantMenuMap;
use App\Reports\MenuOverviewReport;
use App\User;
use \App\Model\ObjectOwnership;
use Illuminate\Support\Facades\Auth;

class MenuController extends SplickitApiCurlController
{

    public function index()
    {
        return view('menu');
    }

    public function load()
    {
        $brand = new Brand();
        $brands = $brand->allBrands();
        $recently_visited = $this->getRecentlyVisited();

        if (in_array('onload_menu_list', session('user_permission_set'))) {
            $search_menus = $this->pageLoadMenus();
        } else {
            $search_menus = $this->pageLoadMenus();
        }

        $response = [
            'brands' => $brands,
            'load_menus' => array_values($search_menus->toArray()),
            'recently_visited' => $recently_visited
        ];
        return $response;
    }

    public function create(Request $request)
    {
        $data = $request->all();

        $this->data = $data;
        $this->data['merchant_ids'] = implode(',', array_column($this->data['merchants'], 'merchant_id'));
        $this->api_endpoint = 'portal/menus';

        $this->audit_data['auditable_type'] = 'Menu_Create';
        $this->audit_data['response_auditable_id'] = 'menu_id';
        $this->audit_data['action'] = 'Create';

        $response = $this->makeCurlRequest(true);

        $new_object_ownership = new ObjectOwnership();

        $new_object_ownership->user_id = Auth::user()->id;
        $new_object_ownership->organization_id = session('user_organization_id');
        $new_object_ownership->object_type = 'menu';
        $new_object_ownership->object_id = $response['menu_id'];

        $new_object_ownership->save();

        if (isset($response['menu_id'])) {
            session(['current_menu_id' => $response['menu_id']]);
        }
        return $response;
    }

    public function update(Request $request)
    {
        $data = $request->all();

        $this->data = $data;
        $this->data['merchant_ids'] = implode(',', array_column($this->data['merchants'], 'merchant_id'));
        $this->api_endpoint = 'portal/menus';

        $response = $this->makeCurlRequest();

        $new_object_ownership = new ObjectOwnership();

        $new_object_ownership->user_id = Auth::user()->id;
        $new_object_ownership->organization_id = session('user_organization_id');
        $new_object_ownership->object_type = 'menu';
        $new_object_ownership->object_id = $response['menu_id'];

        $new_object_ownership->save();

        if (isset($response['menu_id'])) {
            session(['current_menu_id' => $response['menu_id']]);
        }
        return $response;
    }

    public function search(Request $request)
    {
        $data = $request->all();
        $menu = new Menu();

        if (session('user_visibility') == 'global') {
            $result_menus = $menu->searchGlobal($data)->get(['Menu.*']);
        } elseif (session('user_visibility') == 'mine_only') {
            $result_menus = $menu->searchMineOnly($data)->get(['Menu.*']);
        } elseif (session('user_visibility') == 'operator') {
            $result_menus = $menu->searchOperator($data)->get(['Menu.*']);
        } elseif (session('user_visibility') == 'all') {
            $result_menus = $menu->searchOrganizationAll($data)->get(['Menu.*']);
        } elseif (session('user_visibility') == 'brand') {
            $result_menus = $menu->searchBrand($data)->get(['Menu.*']);
        }
        return $result_menus;
    }

    public function pageLoadMenus()
    {
        $menu = new Menu();

        if (session('user_visibility') == 'mine_only') {
            $result_menus = $menu->pageLoadMineOnly();
        } elseif (session('user_visibility') == 'all') {
            $result_menus = $menu->pageLoadAll();
        } elseif (session('user_visibility') == 'brand') {
            $result_menus = $menu->pageLoadBrand();
        } elseif (session('user_visibility') == 'global') {
            $result_menus = $menu->pageLoadGlobal();
        }
        return $result_menus;
    }

    public function addMenuToRecentlyVisited($new_menu)
    {
        $new_menu = [
            'menu_id' => $new_menu->menu_id,
            'name' => $new_menu->name,
            'description' => $new_menu->description,
            'version' => $new_menu->version
        ];

        if (session('recently_visited_menus')) {
            $recently_visited_menus = session('recently_visited_menus');

            //Remove Merchant if It's Already in Recently Visited
            foreach ($recently_visited_menus as $key => $menu) {
                if ($menu['menu_id'] == $new_menu['menu_id']) {
                    unset($recently_visited_menus[$key]);
                    break;
                }
            }

            //Remove the Oldest Recently Visited if We're at the Maximum of 5
            if (count($recently_visited_menus) == 5) {
                array_pop($recently_visited_menus);
            }

            //Add the New Menu to the Beginning of the Array
            array_unshift($recently_visited_menus, $new_menu);
        } else {
            $recently_visited_menus = [];
            $recently_visited_menus[] = $new_menu;
        }
        session(['recently_visited_menus' => $recently_visited_menus]);
    }

    public function getRecentlyVisited()
    {
        if (session('recently_visited_menus')) {
            return session('recently_visited_menus');
        } else {
            return [];
        }
    }

    public function setMenuId($menu_id)
    {
        session(['current_menu_id' => $menu_id]);

        $menu = Menu::find($menu_id);

        $this->addMenuToRecentlyVisited($menu);

        return 1;
    }

    public function getMenu()
    {
        $menu_id = session('current_menu_id');
        if (!is_null($menu_id)) {
            return Menu::find($menu_id)->toArray();
        }
    }

    public function getEditMenu($merchant_id = false)
    {
        $menu_id = session('current_menu_id');

        if (!$merchant_id) {
            $merchant_id = session('operator_merchant_id');
        }

        $this->api_endpoint = 'menus/' . $menu_id . '/getpricelist?merchant_id=' . $merchant_id;

        $this->setMethodGet();
        $this->audit_data['auditable_type'] = 'Menu-QuickEdit';
        $this->audit_data['auditable_id'] = $menu_id;
        $this->audit_data['action'] = 'Get';

        $response = $this->makeCurlRequest(true);

        $response['last_pos_import_date'] = date('m/d/Y h:ma');

        if (isset($response['import_url']))
        {
            $response['import_url'] = str_replace("/app2/portal", "", $response['import_url']);
        } else {
            $response['import_url'] = false;
        }

        //These are for the searchable list
        $response['all_items'] = [];
        $response['all_modifier_items'] = [];
        $included_item_ids = [];

        if (isset($response['menu_types'])) {
            foreach ($response['menu_types'] as $menu_type_name => $menu_type) {
                foreach ($menu_type as $index => $item) {
                    if ($item['active'] == 'Y') {
                        $response['menu_types'][$menu_type_name][$index]['active'] = true;
                        $item['active'] = true;
                    } else {
                        $response['menu_types'][$menu_type_name][$index]['active'] = false;
                        $item['active'] = false;
                    }

                    if (!in_array($item['item_id'], $included_item_ids)) {
                        $included_item_ids[] = $item['item_id'];
                        $response['menu_types'][$menu_type_name][$index]['show_edit_allowed_mods'] = true;
                    } else {
                        $response['menu_types'][$menu_type_name][$index]['show_edit_allowed_mods'] = false;
                    }

                    $response['all_items'][] = $item;
                    $response['menu_types'][$menu_type_name][$index]['success'] = false;
                }
            }
        }
        else {
            $response['menu_types'] = [];
        }


        if (isset($response['modifier_groups'])) {
            foreach ($response['modifier_groups'] as $modifier_group_name => $modifier_group) {
                foreach ($modifier_group as $index => $modifier_item) {
                    if ($modifier_item['active'] == 'Y') {
                        $response['modifier_groups'][$modifier_group_name][$index]['active'] = true;
                    } else {
                        $response['modifier_groups'][$modifier_group_name][$index]['active'] = false;
                    }
                    $response['all_modifier_items'][] = $modifier_item;
                    $response['modifier_groups'][$modifier_group_name][$index]['success'] = false;
                }
            }
        }
        else {
            $response['modifier_groups'] = [];
        }
        return $response;
    }

    public function getMerchantEditMenu($merchant_id)
    {
        session(['merchant_menu_merchant_id' => $merchant_id]);
        $merchant = Merchant::find($merchant_id);
        return ['menu' => $this->getEditMenu($merchant_id), 'merchant' => $merchant];
    }

    public function promoBogoOptions()
    {
        $menu = new Menu();
        return $menu->getPromoBogoOptions(session('current_menu_id'));
    }

    public function getMenusCurrentMerchant(Request $request, Menu $menu)
    {
        try {
            $search_text = $request->search_text;
            $merchant_id = session('current_merchant_id');
            $merchant = Merchant::find($merchant_id);
            $data = [
                'search_text' => $search_text,
                'order_by' => 'Menu.name'
            ];

            $result_menus = null;
            if (session('user_visibility') == 'global') {
                $result_menus = $menu->searchGlobal($data);
            } elseif (session('user_visibility') == 'mine_only') {
                $result_menus = $menu->searchMineOnly($data);
            } elseif (session('user_visibility') == 'operator') {
                $result_menus = $menu->searchOperator($data);
            } elseif (session('user_visibility') == 'all') {
                $result_menus = $menu->searchOrganizationAll($data);
            } elseif (session('user_visibility') == 'brand') {
                $result_menus = $menu->searchBrand($data);
            }
            $response = $result_menus->get(['Menu.*']);
            return response()->json($response, 200);
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 404);
        }
    }

    public function getMenusByBrandID(Request $request, Menu $menu)
    {
        return $menu->getMenusByBrandId($request->brand_id)->get()->toArray();
    }

    public function merchantMenuSearch(Request $request, Merchant $merchant)
    {
        return $merchant->searchMenu($request->search_text, session('current_menu_id'));
    }

    public function basicUpdate(Request $request)
    {
        $data = $request->all();

        $this->data = $data;
        $this->api_endpoint = 'portal/menus/' . $data['menu_id'];

        $this->audit_data['auditable_type'] = 'Menu_Basic-Update';
        $this->audit_data['auditable_id'] = $data['menu_id'];
        $this->audit_data['action'] = 'Update';

        $this->setMethodPost();
        $response = $this->makeCurlRequest(true);

        return $response;
    }

    public function posImport(Request $request)
    {
        $data = $request->all();
        $this->api_endpoint = $data['import_url'];

        $this->audit_data['auditable_type'] = 'Menu_Basic-POSImport';
        $this->audit_data['auditable_id'] = session('current_menu_id');
        $this->audit_data['action'] = 'Import';

        $this->setMethodPost();
        $response = $this->makeCurlRequest(true);
        return $response;
    }

    public function checkQuickEdit()
    {
        $current_menu_id = session('current_menu_id');
        $merchant_menu_merchant_id = session('merchant_menu_merchant_id');

        if (isset($current_menu_id) && isset($merchant_menu_merchant_id)) {
            $merchant_menu_count = MerchantMenuMap::where('menu_id', '=', $current_menu_id)->where('merchant_id', '=', $merchant_menu_merchant_id)->count();

            if ($merchant_menu_count > 0) {
                return ['set' => true];
            } else {
                return ['set' => false];
            }
        } else {
            return ['set' => false];
        }
    }

    public function copyMerchantMenuToMerchant(Request $request) {
        $data = $request->all();

        if (isset($data['source_merchant_id'])) {
            $source_merchant_id = $data['source_merchant_id'];
        }
        else {
            $source_merchant_id = session('merchant_menu_merchant_id');
        }

        if (isset($data['destination_merchant_id'])) {
            $destination_merchant_id = $data['destination_merchant_id'];
        }
        else {
            $destination_merchant_id = session('merchant_menu_merchant_id');
        }

        $this->data = [
            'source_merchant_id' => $source_merchant_id,
            'destination_merchant_id' => $destination_merchant_id
        ];

        $this->api_endpoint = 'portal/menus/' . session('current_menu_id') . '/copypricelist';

        $this->audit_data['auditable_type'] = 'Menu_Basic-MerchMenuCopy';
        $this->audit_data['auditable_id'] = session('current_menu_id');
        $this->audit_data['action'] = 'CopyMenu';

        $this->setMethodPost();
        $response = $this->makeCurlRequest(true);
        return $response;
    }

    public function export($merchantId) {
        $menuOverviewReport = new MenuOverviewReport();

        if ($merchantId == -1) {
            $merchantId = session('operator_merchant_id');
        }

        return $menuOverviewReport->buildReport($merchantId);
    }
}