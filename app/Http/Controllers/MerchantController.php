<?php namespace App\Http\Controllers;


use App\Model\AdmMerchantEmail;
use App\Model\HolidayHour;
use App\Model\Hour;
use App\Model\Merchant;
use App\Model\MerchantMenuMap;
use App\Model\SkinMerchantMap;
use App\Model\Skin;
use App\Model\MerchantMessageMap;
use App\Model\ObjectOwnership;
use App\Model\Tax;
use App\Model\Payment;
use App\Model\Brand;
use App\Model\Traits\StringHelper;
use App\User;
use Cache;
use DB;
use Hamcrest\Util;
use Illuminate\Http\Request;
use \Log;
use Illuminate\Support\Facades\Auth;
use App\Service\Utility;

class MerchantController extends Controller
{
    use StringHelper;

    public function searchByMenu(Request $request, Merchant $merchant)
    {
        $merchants = $merchant->searchMerchantsByMenu(['merchant_id', 'shop_email', 'name', 'display_name', 'city', 'zip'], $request->search_text);
        return response()->json($merchants);
    }

    //Merchant Search
    public function search(Request $request, Merchant $merchant)
    {
        $data = $request->all();

        if (!isset($data['order_by'])) {
            $data['order_by'] = 'Merchant.modified';
        }
        $active_only = $request->active_only ? $request->active_only : false;

        /**
         * We can replace the following code by
         * return $merchant->searchMerchants(['merchant_id', 'shop_email', 'name', 'display_name', 'city', 'zip'], $request->search_text);
         */

        try {
            if (empty($data['brand_id'])) {
                $queryBuilder = $merchant->searchMerchants(['merchant_id', 'shop_email', 'name', 'display_name', 'city', 'zip', 'state'], $request->search_text, $data['order_by']);
            } else {
                $queryBuilder = $merchant->searchMerchantsByBrand(['merchant_id', 'shop_email', 'name', 'display_name', 'city', 'zip', 'state'], $request->search_text, $data['brand_id'], $data['order_by']);
            }

            if ((boolean)$active_only) {
                $queryBuilder = $queryBuilder->where('Merchant.active', '=', 'Y');
            }
            \DB::enableQueryLog();
            $response = $queryBuilder->get();
            $queries = \DB::getQueryLog();
            return response()->json($response, 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 200);
        }
    }

    public function searchPaginated(Request $request, Merchant $merchant)
    {
        $order_by = $request->order_by ? $request->order_by : 'Merchant.modified';
        $order_direction = $request->exists('order_direction') && $request->order_direction == 'false' ? 'ASC' : 'DES';
        $active = $request->exists('active_only') && $request->active_only == 'false' ? null : 'Y';

        if (session('user_visibility') == 'global') {
            $result_merchants = $merchant->searchGlobal($request->search_text, $order_by, $order_direction);
        } elseif (session('user_visibility') == 'mine_only') {
            $result_merchants = $merchant->searchMineOnly($request->search_text, $order_by, $order_direction);
        } elseif (session('user_visibility') == 'operator') {
            $result_merchants = $merchant->searchOperator($request->search_text, $order_by, $order_direction);
        } elseif (session('user_visibility') == 'all') {
            $result_merchants = $merchant->searchOrganizationAll($request->search_text, $order_by, $order_direction);
        } elseif (session('user_visibility') == 'brand') {
            $result_merchants = $merchant->searchBrand($request->search_text, $order_by, $order_direction);
        }

        if ($active == 'Y') {
            $result_merchants = $result_merchants->where('Merchant.active', '=', 'Y');
        }
        $response = $result_merchants->paginate(20);

        $response->getCollection()->transform(function ($item, $key) {
            $item->phone_no = $this->phone_number_format($item->phone_no);
            $item->address1 = $item->address1 . "\n" . $item->city . ", " . $item->state . ' ' . $item->zip;
            return $item;
        });

        return $response;
    }
    //Merchant Search by one or more of its properties.
    public function searchByProperties(Request $request, Merchant $merchant)
    {

        $order_by = $request->order_by ? $request->order_by : 'Merchant.modified';
        $order_direction = $request->exists('order_direction') && $request->order_direction == 'false' ? 'ASC' : 'DES';

        try {

            if (session('user_visibility') == 'global') {
                $result_merchants = $merchant->searchGlobal($request->search_text, $order_by, $order_direction);
            } elseif (session('user_visibility') == 'mine_only') {
                $result_merchants = $merchant->searchMineOnly($request->search_text, $order_by, $order_direction);
            } elseif (session('user_visibility') == 'operator') {
                $result_merchants = $merchant->searchOperator($request->search_text, $order_by, $order_direction);
            } elseif (session('user_visibility') == 'all') {
                $result_merchants = $merchant->searchOrganizationAll($request->search_text, $order_by, $order_direction);
            } elseif (session('user_visibility') == 'brand') {
                $result_merchants = $merchant->searchBrand($request->search_text, $order_by, $order_direction);
            }

            $response = $result_merchants->get();

            return response()->json($response, 200);

        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 200);
        }
    }

    //Filter Merchants by First Letter for Home Screen
    public function firstLetterFilter(Request $request, Merchant $merchant)
    {
        $order_by = $request->order_by ? $request->order_by : 'Merchant.modified';
        $order_direction = $request->exists('order_direction') && $request->order_direction == 'false' ? 'ASC' : 'DES';
        $active = $request->exists('active_only') && $request->active_only == 'false' ? null : 'Y';

        if (session('user_visibility') == 'global') {
            $result_merchants = $merchant->firstLetterFilterSuperUser($request->search_text, $order_by, $order_direction);
        } elseif (session('user_visibility') == 'mine_only') {
            $result_merchants = $merchant->firstLetterFilterMineOnly($request->search_text, $order_by, $order_direction);
        } elseif (session('user_visibility') == 'operator') {
            $result_merchants = $merchant->firstLetterFilterOperator($request->search_text, $order_by, $order_direction);
        } elseif (session('user_visibility') == 'all') {
            $result_merchants = $merchant->firstLetterFilterOrganizationAll($request->search_text, $order_by, $order_direction);
        } elseif (session('user_visibility') == 'brand') {
            $result_merchants = $merchant->firstLetterFilterBrand($request->search_text, $order_by, $order_direction);
        }

        if ($active == 'Y') {
            $result_merchants = $result_merchants->where('Merchant.active', '=', 'Y');
        }

        $response = $result_merchants->paginate(20);

        $response->getCollection()->transform(function ($item, $key) {
            $item->phone_no = $this->phone_number_format($item->phone_no);
            $item->address1 = $item->address1 . "\n" . $item->city . ", " . $item->state . ' ' . $item->zip;
            return $item;
        });
        return $response;
    }

    public function load()
    {
        $recently_visited = $this->getRecentlyVisited();
        $page_load_list = $this->pageLoadMerchants();
        return ['load_list' => $page_load_list, 'recently_visited' => $recently_visited];

    }

    public function addMerchantToRecentlyVisited($new_merchant)
    {
        $new_merchant = [
            'merchant_id' => $new_merchant->merchant_id,
            'name' => $new_merchant->name,
            'address1' => substr($new_merchant->address1, 0, 20),
            'address2' => $new_merchant->city . ", " . $new_merchant->state . " " . $new_merchant->zip,
            'phone_no' => $new_merchant->phone_no,
        ];

        if (session('recently_visited_merchants')) {
            $recently_visited_merchants = session('recently_visited_merchants');

            //Remove Merchant if It's Already in Recently Visited
            foreach ($recently_visited_merchants as $key => $merchant) {
                if ($merchant['merchant_id'] == $new_merchant['merchant_id']) {
                    unset($recently_visited_merchants[$key]);
                    break;
                }
            }

            //Remove the Oldest Recently Visited if We're at the Maximum of 5
            if (count($recently_visited_merchants) == 5) {
                array_pop($recently_visited_merchants);
            }

            //Add the New Merchant to the Beginning of the Array
            array_unshift($recently_visited_merchants, $new_merchant);
        } else {
            $recently_visited_merchants = [];
            $recently_visited_merchants[] = $new_merchant;
        }
        session(['recently_visited_merchants' => $recently_visited_merchants]);
    }

    //Gets the General Info Form Data
    public function generalInfo()
    {
        $merchant_id = session('current_merchant_id');

        $merchant = Merchant::find($merchant_id);

        $merchant = $merchant->toArray();

        return $merchant;
    }

    //Set the Current Merchant ID that the user is Editing
    public function setCurrent(Request $request, Merchant $merchant)
    {
        $data = $request->all();

        $merchant = $merchant->getMerchantWithMenus($data['merchant_id']);
        session(['current_brand_id' => $merchant->brand_id]);
        session(['current_merchant_id' => $merchant->merchant_id]);
        session(['operator_merchant_id' => $merchant->merchant_id]);

        if (isset($merchant->menu()->get()[0]->menu_id)) {
            session(['current_menu_id' => $merchant->menu()->get()[0]->menu_id]);
            session(['merchant_menu_merchant_id' => $merchant->merchant_id]);
        }

        $this->addMerchantToRecentlyVisited($merchant);

        return $merchant;
    }

    //Set the Current Merchant ID that the user is Editing
    public function viewMerchant($merchant_id)
    {
        $merchant = Merchant::find($merchant_id);

        session(['current_brand_id' => $merchant->brand_id]);
        session(['current_merchant_id' => $merchant_id]);

        $this->addMerchantToRecentlyVisited($merchant);

        $this->index();
    }

    public function getRecentlyVisited()
    {
        if (session('recently_visited_merchants')) {
            return session('recently_visited_merchants');
        } else {
            return [];
        }
    }

    public function getCurrentMerchant(Merchant $merchant)
    {
        $merchantId = session('current_merchant_id');
        return $merchant->getMerchantWithMenus($merchantId);
    }

    public function createNewMerchant(Request $request)
    {
        $data = $request->all();

        $user = new User();

        if (!$user->checkPermission('brands_filter')) {
            $data['brand'] = session('brand_manager_brand');
        }
        return $this->createSingleMerchant($data);
    }

    public function createSingleMerchant($data)
    {

        $utility = new Utility();

        $merchant = new Merchant();

        $merchant->name = $data['name'];
        $merchant->brand_id = $data['brand'];
        $merchant->phone_no = $data['phone_no'];

        $merchant->address1 = $data['address1'];

        if (isset($data['address2'])) {
            $merchant->address2 = $data['address2'];
        }

        $merchant->city = $data['city'];
        $merchant->state = $data['state'];
        $merchant->zip = $data['zip'];
        $merchant->country = $data['country'];

        if (isset($data['merchant_external_id'])) {
            $merchant->merchant_external_id = $data['merchant_external_id'];
        }

        if (isset($data['display_name'])) {
            $merchant->display_name = $data['display_name'];
        }

        $merchant->time_zone = $data['time_zone']['type_id_value'];
        $merchant->time_zone_string = $utility->convertOffsetToTimeZoneString($data['time_zone']['type_id_value']);

        $merchant->shop_email = $data['shop_email'];

        $merchant->save();

        $new_object_ownership = new ObjectOwnership();

        $new_object_ownership->user_id = Auth::user()->id;
        $new_object_ownership->organization_id = session('user_organization_id');
        $new_object_ownership->object_type = 'merchant';
        $new_object_ownership->object_id = $merchant->merchant_id;

        $new_object_ownership->save();

        try {
            $brand_skin = Skin::where('brand_id', '=', $merchant->brand_id)->first();

            //Create a mapping to Merchant's Brand's Skin
            $brand_merchant_map = new SkinMerchantMap();

            $brand_merchant_map->merchant_id = $merchant->merchant_id;
            $brand_merchant_map->skin_id = $brand_skin->skin_id;

            $brand_merchant_map->save();
        } catch (\Exception $e) {
            Log::error('A skin id was not found for brand ' . $merchant->brand_id);
            Log::error($e);
        }

        $brand_merchant_map = new SkinMerchantMap();

        $brand_merchant_map->merchant_id = $merchant->merchant_id;
        $brand_merchant_map->skin_id = 153;

        $brand_merchant_map->save();

        session(['current_brand_id' => $merchant->brand_id]);
        session(['current_merchant_id' => $merchant->merchant_id]);

        return 1;
    }

    public function getProgressToCompletion()
    {
        $merchant_id = session('current_merchant_id');

        $progress_milestones = [
            'hours' => ['progress_percentage' => 10],
            'sales_tax' => ['progress_percentage' => 10],
            'order_receiving' => ['progress_percentage' => 10],
            'admin_email' => ['progress_percentage' => 10],
            'holiday_hour' => ['progress_percentage' => 10],
            'menu' => ['progress_percentage' => 10],
            'skin_association' => ['progress_percentage' => 10],
            'payment_group' => ['progress_percentage' => 10]
        ];

        //Hours Saved Check
        $hour_count = Hour::where('merchant_id', '=', $merchant_id)->where('hour_type', '=', 'R')->count();

        if ($hour_count > 0) {
            $progress_milestones['hours']['complete'] = true;
        } else {
            $progress_milestones['hours']['complete'] = false;
        }

        //Order Receiving Saved Check
        $order_receiving_count = MerchantMessageMap::where('merchant_id', '=', $merchant_id)->count();

        if ($order_receiving_count > 0) {
            $progress_milestones['order_receiving']['complete'] = true;
        } else {
            $progress_milestones['order_receiving']['complete'] = false;
        }

        //Admin Email Saved Check
        $admin_email_count = AdmMerchantEmail::where('merchant_id', '=', $merchant_id)->count();

        if ($admin_email_count > 0) {
            $progress_milestones['admin_email']['complete'] = true;
        } else {
            $progress_milestones['admin_email']['complete'] = false;
        }

        //Holiday Hours Saved Check
        $holiday_hour_count = HolidayHour::where('merchant_id', '=', $merchant_id)->count();

        if ($holiday_hour_count > 0) {
            $progress_milestones['holiday_hour']['complete'] = true;
        } else {
            $progress_milestones['holiday_hour']['complete'] = false;
        }

        //Menu Saved Check
        $menu_count = MerchantMenuMap::where('merchant_id', '=', $merchant_id)->count();

        if ($menu_count > 0) {
            $progress_milestones['menu']['complete'] = true;
        } else {
            $progress_milestones['menu']['complete'] = false;
        }

        //Tax Saved Check
        $tax_count = Tax::where('merchant_id', '=', $merchant_id)->where('tax_group', '=', 1)->count();

        if ($tax_count > 0) {
            $progress_milestones['sales_tax']['complete'] = true;
        } else {
            $progress_milestones['sales_tax']['complete'] = false;
        }

        //Skin Association Check
        $skin_count = SkinMerchantMap::where('merchant_id', '=', $merchant_id)->count();
        if ($skin_count > 0) {
            $progress_milestones['skin_association']['complete'] = true;
        } else {
            $progress_milestones['skin_association']['complete'] = false;
        }

        //Payment Group Association Check
        $payment_count = Payment::where('merchant_id', '=', $merchant_id)->count();
        if ($payment_count > 0) {
            $progress_milestones['payment_group']['complete'] = true;
        } else {
            $progress_milestones['payment_group']['complete'] = false;
        }

        return $progress_milestones;
    }

    public function all()
    {
        if (in_array('super_usr', session('user_permissions'))) {
            return Merchant::orderBy('name')->get(['name', 'merchant_id', 'address1', 'city', 'state', 'zip'])->toArray();
        }
    }

    public function setMenuRelated(Request $request, MerchantMenuMap $map)
    {
        if (!$request->has('selected_menus') ||
            !$request->session()->has('current_merchant_id') ||
            $request->session()->get('current_merchant_id') == "") {
            return $this->errorResponse("invalid request", 404);
        }
        $selected_menus = $request->input('selected_menus');

        $current_merchant_id = session('current_merchant_id');

        $res = $map->setRelatedMerchantMenuConfiguration($current_merchant_id, $selected_menus);
        return response()->json($res);
    }

    public function pageLoadMerchants()
    {
        $merchant = new Merchant();

        if (session('user_visibility') == 'mine_only') {
            $result_merchants = $merchant->pageLoadMineOnly();
        } elseif (session('user_visibility') == 'all') {
            $result_merchants = $merchant->pageLoadAll();
        } elseif (session('user_visibility') == 'brand') {
            $result_merchants = $merchant->pageLoadBrand();
        } elseif (session('user_visibility') == 'global') {
            $result_merchants = $merchant->pageLoadGlobal();
        } elseif (session('user_visibility') == 'operator') {
            $result_merchants = $merchant->pageLoadOperator();
        }
        return $result_merchants;
    }

    public function setMultiBrand(Request $request)
    {
        $user = new User();
        $data = $request->all();

        if ($user->checkPermission('brands_filter')) {
            session(['multi_merchant_brand' => $data['brand']]);
        } else {
            session(['multi_merchant_brand' => session('brand_manager_brand')]);
        }
    }

    public function multiMerchantUpload(Request $request)
    {
        $response = [];
        $request_data = $request->all();

        $user = new User();
        if ($user->checkPermission('brands_filter')) {
            $multi_merchant_brand = $request_data['brand'];
            session(['multi_merchant_brand' => $request_data['brand']]);
        } else {
            $multi_merchant_brand = session('brand_manager_brand');
        }

        $brand = Brand::find($multi_merchant_brand);

        $response['valid'] = true;
        $response['errors'] = [];

        $time_zones = [-5, -6, -7, -8, -9];

        $merchants_to_be_created = [];

        $row = 1;

        $csv_file = fopen($_FILES['key']['tmp_name'], "r");

        while (($data = fgetcsv($csv_file, 0, ",")) !== FALSE) {

            if ($data[1]== 'Store Email') {
                continue;
            }

            if (isset($data[9])) {
                if (!in_array($data[9], $time_zones)) {
                    $response['errors'][] = 'The value for row ' . $row . ' column 10 "' . $data[9] . '" is not a valid timezone.';
                    $response['valid'] = false;
                }
            } else {
                $response['errors'][] = 'No timezone was set for row ' . $row;
                $response['valid'] = false;
            }

            if (isset($data[1])) {
                if (!(filter_var($data[1], FILTER_VALIDATE_EMAIL))) {
                    $response['errors'][] = 'The value for row ' . $row . ' column 2 "' . $data[1] . '" is not a valid email.';
                    $response['valid'] = false;
                }
            } else {
                $response['errors'][] = 'No shop email was set for row ' . $row;
                $response['valid'] = false;
            }

            $merchant_data = [];

            $merchant_data['display_name'] = trim($data[2]);

            $merchant_data['name'] = $brand->brand_name;

            $merchant_data['brand'] = $multi_merchant_brand;

            $merchant_data['phone_no'] = trim($data[9]);
            $merchant_data['merchant_external_id'] = trim($data[0]);

            $merchant_data['address1'] = trim($data[3]);
            $merchant_data['address2'] = trim($data[4]);

            $merchant_data['city'] = trim($data[5]);
            $merchant_data['state'] = trim($data[6]);
            $merchant_data['zip'] = trim($data[7]);
            $merchant_data['phone_no'] = trim($data[8]);
            $merchant_data['time_zone']['type_id_value'] = trim($data[9]);
            $merchant_data['country'] = 'US';

            $merchant_data['shop_email'] = trim($data[1]);

            $merchants_to_be_created[] = $merchant_data;
            $row++;
        }

        if ($response['valid']) {
            foreach ($merchants_to_be_created as $data) {
                $this->createSingleMerchant($data);
            }
            $response['merchant_count'] = sizeof($merchants_to_be_created);

        }
        return $response;
    }
}