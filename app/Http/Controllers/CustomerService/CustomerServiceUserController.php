<?php namespace App\Http\Controllers\CustomerService;

use App\Http\Controllers\SplickitApiCurlController;
use App\Model\BalanceChange;
use App\Model\CustomerServiceUser;
use App\Model\CustomerServiceUserProd;
use App\Model\DeviceBlacklist;
use App\Model\Orders;
use App\Model\UserBrandLoyaltyHistory;
use App\Model\UserBrandPointsMap;
use App\Model\UserDeliveryLocation;
use App\CustomerServiceSearch\UserSearch;
use Cache;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\User;

class CustomerServiceUserController extends SplickitApiCurlController
{
    public $user_search;
    public $query_offset;
    public $customer_service_user;

    public function paginatedSearch(Request $request, CustomerServiceUser $customerServiceUser)
    {
        try {
            $user_search = new UserSearch();

            $results = $user_search->applyFilter($request);

            return $results;
//            if (!is_null($request->order_by)) {
//                $order_by = $request->order_by;
//                $order_direction = $request->exists('order_direction') && $request->order_direction == 'false' ? 'ASC' : 'DES';
//                $result = $customerServiceUser->getCurrentSearchRecordSet()->orderBy($order_by, $order_direction)->paginate(20);
//            }
//            else {
//                $result = $customerServiceUser->getCurrentSearchRecordSet()->paginate(20);
//            }
//            return response()->json($result, 200);
        } catch (\Exception $exception) {
            return response()->json(['errors' => $exception], 404);
        }
    }


    public function addUserToRecentlyVisited($new_user)
    {

        if (session('recently_visited_users')) {
            $recently_visited_users = session('recently_visited_users');

            //Remove Merchant if It's Already in Recently Visited
            foreach ($recently_visited_users as $key => $user) {
                if ($user['user_id'] == $new_user['user_id']) {
                    unset($recently_visited_users[$key]);
                    break;
                }
            }

            //Remove the Oldest Recently Visited if We're at the Maximum of 5
            if (count($recently_visited_users) == 5) {
                array_pop($recently_visited_users);
            }

            //Add the New Merchant to the Beginning of the Array
            array_unshift($recently_visited_users, $new_user);
        } else {
            $recently_visited_users = [];
            $recently_visited_users[] = $new_user;
        }
        session(['recently_visited_users' => $recently_visited_users]);
    }
    public function deleteUserFromRecentlyVisited($user_obj)
    {
        if (session('recently_visited_users')) {
            $recently_visited_users = session('recently_visited_users');

            //Remove Merchant if It's Already in Recently Visited
            foreach ($recently_visited_users as $key => $user) {

                if ($user['user_id'] == $user_obj['user_id']) {
                    unset($recently_visited_users[$key]);
                    break;
                }
            }
            session(['recently_visited_users' => $recently_visited_users]);
        }
    }

    public function openEditUser(Request $request)
    {
        $data = $request->all();

        $delivery_location = UserDeliveryLocation::where('user_id', '=', $data['user_id'])->orderBy('user_addr_id', 'desc')->take(1)->get(['user_addr_id', 'address1', 'address2', 'state', 'city', 'zip']);

        $this->addUserToRecentlyVisited($data);

        return $delivery_location->toArray()[0];

    }

    public function setCurrentUser(Request $request)
    {
        $data = $request->all();

        session(['current_user_id' => $data['user_id']]);

        return 1;
    }

    public function index()
    {
        $user_id = session('current_user_id');

        $user = CustomerServiceUser::find($user_id)->toArray();

        if (substr($user['flags'], 0, 1) == 'X') {
            $user['blacklisted'] = true;
        }
        else {
            $user['blacklisted'] = false;
        }

        $this->addUserToRecentlyVisited($user);

        return $user;
    }

    public function getRecentlyVisited()
    {
        if (session('recently_visited_users')) {
            return session('recently_visited_users');
        } else {
            return [];
        }
    }

    public function updateUser(Request $request, CustomerServiceUser $model)
    {
        $data = $request->all();
        $user = $model->getById($data['user_id']);

        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->email = $data['email'];

        $user->contact_no = $data['contact_no'];

        $user->save();

        return 1;
    }

    public function getDeliveryLocations()
    {
        $user_id = session('current_user_id');
        $delivery_locations = UserDeliveryLocation::where('user_id', '=', $user_id)
            ->get(['address1', 'address2', 'name', 'city', 'state', 'zip', 'phone_no', 'instructions']);

        return $delivery_locations->toArray();
    }

    public function getOrderHistory()
    {
        $user_id = session('current_user_id');

        if (session('user_visibility') == 'global') {
            $orders = Orders::where('user_id', '=', $user_id)
                ->get(['order_id', 'order_dt_tm', 'order_amt', 'order_qty', 'grand_total', 'promo_code', 'promo_amt', 'status']);
        } elseif (session('user_visibility') == 'brand') {
            $orders = DB::connection('reports_db')->table('Orders')
                ->join('Merchant', 'Orders.merchant_id', '=', 'Merchant.merchant_id')
                ->join('Brand2', 'Merchant.brand_id', '=', 'Brand2.brand_id')
                ->join('portal_brand_manager_brand_map', 'Merchant.brand_id', '=', 'portal_brand_manager_brand_map.brand_id')
                ->where('Orders.user_id', '=', $user_id)
                ->where('portal_brand_manager_brand_map.user_id', '=', Auth::user()->id)
                ->get(['order_id', 'order_dt_tm', 'order_amt', 'order_qty', 'grand_total', 'promo_code', 'promo_amt', 'status']);
        } elseif (session('user_visibility') == 'operator') {
            $orders = DB::connection('reports_db')->table('Orders')
                ->join('portal_operator_merchant_map', 'Merchant.merchant_id', '=', 'portal_operator_merchant_map.merchant_id')
                ->where('Orders.user_id', '=', $user_id)
                ->where('portal_operator_merchant_map.user_id', '=', Auth::user()->id)
                ->get(['order_id', 'order_dt_tm', 'order_amt', 'order_qty', 'grand_total', 'promo_code', 'promo_amt', 'status']);
        }
        return $orders->toArray();
    }

    public function getRefundHistory()
    {
        $user_id = session('current_user_id');

        if (session('user_visibility') == 'global') {
            $refund_history = BalanceChange::where('user_id', '=', $user_id)
                ->get(['id', 'created', 'balance_before', 'charge_amt', 'balance_after', 'process', 'notes', 'order_id']);
        } elseif (session('user_visibility') == 'brand') {
            $refund_history = DB::table('Balance_Change')
                ->join('Orders', 'Balance_Change.order_id', '=', 'Orders.order_id')
                ->join('Merchant', 'Orders.merchant_id', '=', 'Merchant.merchant_id')
                ->join('Brand2', 'Merchant.brand_id', '=', 'Brand2.brand_id')
                ->join('portal_brand_manager_brand_map', 'Merchant.brand_id', '=', 'portal_brand_manager_brand_map.brand_id')
                ->where('Orders.user_id', '=', $user_id)
                ->where('portal_brand_manager_brand_map.user_id', '=', Auth::user()->id)
                ->get(['Balance_Change.id', 'Balance_Change.created', 'Balance_Change.balance_before', 'Balance_Change.charge_amt', 'Balance_Change.balance_after', 'Balance_Change.process', 'Balance_Change.notes', 'Balance_Change.order_id']);
        } elseif (session('user_visibility') == 'operator') {
            $refund_history = DB::table('Balance_Change')
                ->join('Orders', 'Balance_Change.order_id', '=', 'Orders.order_id')
                ->join('portal_operator_merchant_map', 'Merchant.merchant_id', '=', 'portal_operator_merchant_map.merchant_id')
                ->where('Orders.user_id', '=', $user_id)
                ->where('portal_operator_merchant_map.user_id', '=', Auth::user()->id)
                ->get(['Balance_Change.id', 'Balance_Change.created', 'Balance_Change.balance_before', 'Balance_Change.charge_amt', 'Balance_Change.balance_after', 'Balance_Change.process', 'Balance_Change.notes', 'Balance_Change.order_id']);
        }
        return $refund_history->toArray();
    }

    public function getBrandLoyaltyHistory()
    {
        $user_id = session('current_user_id');
        if (session('user_visibility') == 'global') {
            $brand_loyalty = UserBrandLoyaltyHistory::where('user_id', '=', $user_id)
                ->with(['brand'])
                ->get();
        } elseif (session('user_visibility') == 'brand') {
            $brand_loyalty = DB::table('User_Brand_Loyalty_History')
                ->join('portal_brand_manager_brand_map', 'User_Brand_Loyalty_History.brand_id', '=', 'portal_brand_manager_brand_map.brand_id')
                ->where('User_Brand_Loyalty_History.user_id', '=', $user_id)
                ->where('portal_brand_manager_brand_map.user_id', '=', Auth::user()->id)
                ->get();
        } elseif (session('user_visibility') == 'operator') {
            $brand_loyalty = DB::table('User_Brand_Loyalty_History')
                ->join('Merchant', 'User_Brand_Loyalty_History.brand_id', '=', 'Merchant.brand_id')
                ->join('portal_operator_merchant_map', 'Merchant.merchant_id', '=', 'portal_operator_merchant_map.merchant_id')
                ->where('User_Brand_Loyalty_History.user_id', '=', $user_id)
                ->where('portal_operator_merchant_map.user_id', '=', Auth::user()->id)
                ->get();
        }

        return $brand_loyalty->toArray();
    }

    public function getLoyaltyDetailsForUserSelection(Request $request)
    {
        if ($request->brand_id) {
            $user_id = session('current_user_id');
            $brand_loyalty = [];
            if (session('user_visibility') == 'global') {
                $brand_loyalty = UserBrandLoyaltyHistory::where('user_id', '=', $user_id)
                    ->with(['brand'])->where('brand_id', '=', $request->brand_id)
                    ->orderBy('created', 'DESC')
                    ->get()->toArray();
            } elseif (session('user_visibility') == 'brand') {
                $brand_loyalty = DB::table('User_Brand_Loyalty_History')
                    ->join('portal_brand_manager_brand_map', 'User_Brand_Loyalty_History.brand_id', '=', 'portal_brand_manager_brand_map.brand_id')
                    ->where('User_Brand_Loyalty_History.user_id', '=', $user_id)
                    ->where('portal_brand_manager_brand_map.user_id', '=', Auth::user()->id)
                    ->get()->toArray();
            } elseif (session('user_visibility') == 'operator') {
                $brand_loyalty = DB::table('User_Brand_Loyalty_History')
                    ->join('Merchant', 'User_Brand_Loyalty_History.brand_id', '=', 'Merchant.brand_id')
                    ->join('portal_operator_merchant_map', 'Merchant.merchant_id', '=', 'portal_operator_merchant_map.merchant_id')
                    ->where('User_Brand_Loyalty_History.user_id', '=', $user_id)
                    ->where('portal_operator_merchant_map.user_id', '=', Auth::user()->id)
                    ->get()->toArray();
            }

            return response()->json($brand_loyalty, 200);
        }

        return response()->json([], 200);
    }

    public function getBrandLoyalty()
    {
        $user_id = session('current_user_id');
        if (session('user_visibility') == 'global') {
            $brand_loyalty_details = UserBrandPointsMap::where('user_id', '=', $user_id)
                ->with(['brand'])
                ->get();
        } elseif (session('user_visibility') == 'brand') {
            $brand_loyalty_details = DB::table('User_Brand_Points_Map')
                ->join('portal_brand_manager_brand_map', 'User_Brand_Points_Map.brand_id', '=', 'portal_brand_manager_brand_map.brand_id')
                ->where('User_Brand_Points_Map.user_id', '=', $user_id)
                ->where('portal_brand_manager_brand_map.user_id', '=', Auth::user()->id)
                ->get();
        } elseif (session('user_visibility') == 'operator') {
            $brand_loyalty_details = DB::table('User_Brand_Points_Map')
                ->join('Merchant', 'User_Brand_Points_Map.brand_id', '=', 'Merchant.brand_id')
                ->join('portal_operator_merchant_map', 'Merchant.merchant_id', '=', 'portal_operator_merchant_map.merchant_id')
                ->where('User_Brand_Points_Map.user_id', '=', $user_id)
                ->where('portal_operator_merchant_map.user_id', '=', Auth::user()->id)
                ->get();
        }
        return $brand_loyalty_details->toArray();
    }

    public function adjustLoyalty(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = session('current_user_id');
        $this->data = $data;

        $portal_user = new User();

        if ($portal_user->checkPermission('brands_filter')) {
            $brand_id = $request->brand_id;
        } else {
            $brand_id = session('brand_manager_brand');
        }

        $this->api_endpoint = "brands/$brand_id/adjustloyaltypoints";
        $response = $this->makeCurlRequest();

        if (array_key_exists('error_message', $response)) {
            return response()->json(['error_message' => $response['error_message']], $response['error_code']);
        }

        if (array_key_exists('user_brand_loyalty_info', $response)) {
            $new_loyalty_record = $response['user_brand_loyalty_info'];

            return response()->json($new_loyalty_record, 200);
        }
        return response()->json($response, 404);
    }

    public function makeDefaultBrandLoyaltyAccount() {

        $this->api_endpoint = 'brands/'.session('brand_manager_brand').'/loyalty/users/'.session('current_user_id').'/setasprimaryaccount';
        $this->data['user_id'] = session('current_user_id');

        $response = $this->makeCurlRequest();
        return $response;
    }

    public function blacklistUser(Request $request) {
        $data = $request->all();

        $user = CustomerServiceUser::find(session('current_user_id'));

        $this->blacklistSingleUser($user->user_id, $data['blacklist_note']);

        $other_blacklisted_accounts = [];

        if (!is_null($user->contact_no)) {
            $users = CustomerServiceUser::where('contact_no', '=', $user->contact_no)
                ->where('user_id', '!=', $user->user_id)
                ->get()->toArray();

            foreach ($users as $additional_user) {
                $this->blacklistSingleUser($additional_user['user_id'], 'autoblacklisted-user-'.$user->user_id);
                $other_blacklisted_accounts[] = $user->user_id;
            }
        }

        return [
            'other_blacklisted_accounts' => $other_blacklisted_accounts
        ];
    }

    public function deleteUser(Request $request){

        $data = $request->all();
        $user = CustomerServiceUser::find(session('current_user_id'));

        if($data['delete'] == true){
            $this->api_endpoint = 'users/'.$user->uuid.'/privacywipe';
            $this->audit_data['auditable_type'] = 'User-Delete-user';
            $this->audit_data['auditable_id'] = $user->user_id;
            $this->audit_data['action'] = 'DeleteUser';

            $this->setMethodDelete();
            $response = $this->makeCurlRequest(true);
            if (isset($response['error'])) {
                Log::info("We have a failure in request");
                return $response['error'];
            }else if (isset($response['status']) && $response['status'] == 'success') {
                $this->deleteUserFromRecentlyVisited($user);
                return $response;
            }else if (isset($response['result']) && $response['result'] == 'success') {
                $this->deleteUserFromRecentlyVisited($user);
                return $response;
            }else if (isset($response) && $response != null) {
                return $response;
            } else {
                Log::info("We have a failure in the request");
                return array("error" => array("error_message" => "There was an unknown error on the request"));
            }
        }
        return;

    }
    public function blacklistSingleUser($user_id, $blacklist_note) {
        $user = CustomerServiceUserProd::find($user_id);

        $user->custom_message = $blacklist_note;
        $user->logical_delete = 'Y';
        $user->flags = 'X'.substr($user->flags, 1);

        $user->save();

        if (is_null($user->device_id)) {
            $black_list_device = 'userid-'.$user_id;
        }
        else {
            $black_list_device = $user->device_id;
        }

        $device_blacklist = DeviceBlacklist::firstOrNew(['device_id'=>$black_list_device]);

        $device_blacklist->device_id = $black_list_device;

        $device_blacklist->save();
    }
}