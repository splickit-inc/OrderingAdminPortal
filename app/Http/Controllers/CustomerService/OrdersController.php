<?php namespace App\Http\Controllers\CustomerService;

use App\Model\Orders;
use App\Model\Merchant;
use App\Model\CustomerServiceUserProd;
use App\Model\AdmOrderReversal;
use App\Model\SmawvNb7DayOrders;
use Carbon\Carbon;
use App\Model\MerchantMessageHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Model\SmawLiveOrders;
use App\Service\SplickitAdminCurlService;
use \App\Http\Controllers\Controller;
use \App\Service\Utility;
use \App\CustomerServiceSearch\OrdersSearch;
use \DB;

use Illuminate\Support\Facades\Log;

class OrdersController extends Controller
{

    protected $statuses = ['OPEN', 'EXECUTED', 'CANCELLED', 'CANCEL'];

    /** @var Builder */
    protected $orders_query;

    public $order_event;
    public $order_by;

    public function index()
    {

        if (session('recently_visited_orders')) {
            $recently_visited_orders = session('recently_visited_orders');
        } else {
            $recently_visited_orders = [];
        }

        /** @var Builder $last_25_orders */
        $last_25_orders = null;
        if (session('user_visibility') == 'global') {
            $last_25_orders = SmawvNb7DayOrders::take(25);
        } elseif (session('user_visibility') == 'brand') {
            $last_25_orders = SmawvNb7DayOrders::
            join('Merchant', 'smawv_nb_7day_orders.merchant_id', '=', 'Merchant.merchant_id')
                ->where('Merchant.brand_id', '=', session('brand_manager_brand'))
                ->take(25);
        }

        $last_25_orders = $last_25_orders
            ->orderBy('order_dt_tm', 'DESC')
            ->whereNotIn('smawv_nb_7day_orders.order_id', function ($query) {
                $query->select('smawv_nb_7day_orders.order_id')->where('status', '=', 'Y')->where('order_dt_tm', '<', Carbon::now()->subHours(48))->from('smawv_nb_7day_orders');
            })
            ->leftJoin(DB::raw('(SELECT `Catering_Orders`.`order_id`,\'Catering\' as is_catering FROM Catering_Orders) as Catering_Orders'), 'smawv_nb_7day_orders.order_id', '=', 'Catering_Orders.order_id')
            ->select(['smawv_nb_7day_orders.*', DB::raw('CONCAT_WS(\' \', `is_catering`,`smawv_nb_7day_orders`.`order_type`) as order_type_complete')]);

        return response()->json(['recently_visited_orders' => $recently_visited_orders, 'last_25_orders' => $last_25_orders->get()], 200);
    }

    public function getOrder()
    {
        $order = session('current_order');
        $order_id = $order['order_id'];

        $order = Orders::find($order_id);

        $order_merchant = Merchant::find($order->merchant_id);
        $order_user = CustomerServiceUserProd::find($order->user_id);

        $order = $order->toArray();
        $order['merchant'] = $order_merchant;
        $order['order_user'] = $order_user;

        $this->addOrderToRecentlyVisited($order);

        $merchant_message_history = MerchantMessageHistory::where('order_id', '=', $order_id)->with('messageFormat')->get()->toArray();

        $order_reversal_count = AdmOrderReversal::where('order_id', '=', $order_id)->count();

        if ($order_reversal_count > 0 || ($order['status'] != 'O' && $order['status'] != 'E')) {

            if ($order_reversal_count > 0) {
                $order_reversal = AdmOrderReversal::where('order_id', '=', $order_id)->first();
                $order['refund_note'] = $order_reversal->note;
            }
            else {
                $order['refund_note'] = 'The order is not in a status that can be refunded (Open or Executed Properly).';
            }

            $order['refunded'] = true;
        }
        else {
            $order['refunded'] = false;
        }

        foreach ($merchant_message_history as $index => $message) {
            $merchant_message_history[$index]['truncated_response'] = substr($message['response'], 0, 20) . '...';
        }
        return ['order' => $order, 'merchant_message_history' => $merchant_message_history];
    }

    public function paginatedSearch(Request $request)
    {
        $order_search = new OrdersSearch();
        $results = $order_search->applyFilter($request);

        return $results;
//        try {
//            $this->order_by = $request->order_by ? $request->order_by : 'order_id';
//            $order_direction = $request->exists('order_direction') && $request->order_direction == 'false' ? 'ASC' : 'DES';
//            $past_seven_days = $request->exists('past_seven_days') && $request->past_seven_days == 'false' ? false : true;
//            $this->setSearchConditionalWhereClause([
//                'search_text' => $request->search_text,
//                'past_seven_days' => $past_seven_days,
//
//            ]);
//
//            $result = $this->orders_query->orderBy($this->order_by, $order_direction)->paginate(20);
//
//            return response()->json($result, 200);
//        } catch (\Exception $exception) {
//            return response()->json(['errors' => $exception], 404);
//        }
    }

    public function getFutureOrders(Request $request)
    {
        try {
            $this->order_by = $request->order_by ? $request->order_by : 'order_id';
            $order_direction = $request->exists('order_direction') && $request->order_direction == 'false' ? 'ASC' : 'DES';
            $past_seven_days = $request->exists('past_seven_days') && $request->past_seven_days == 'false' ? false : true;

            $this->setSearchConditionalWhereClause([
                'search_text' => $request->search_text,
                'past_seven_days' => $past_seven_days,
            ]);

            $result = $this->orders_query->where('pickup_dt_tm', '>=', Carbon::now())->orderBy($this->order_by, $order_direction)->paginate(20);

            return response()->json($result, 200);
        } catch (\Exception $exception) {
            return response()->json(['errors' => $exception], 404);
        }
    }

    public function getRecentlyVisited(Request $request)
    {
        try {
            if (session('recently_visited_orders')) {
                $recently_visited_orders = session('recently_visited_orders');
            } else {
                $recently_visited_orders = [];
            }

            return response()->json($recently_visited_orders, 200);
        } catch (\Exception $exception) {
            return response()->json(['errors' => $exception], 404);
        }
    }

    public function search(Request $request)
    {
        $data = $request->all();

        $this->setSearchConditionalWhereClause($data);

        $full_record_count = $this->searchTotalCount();

        $this->query_offset = 0;

        $this->order_by = $data['order_by'];

        $order_records = $this->getCurrentSearchRecordSet();

        return ['full_record_count' => $full_record_count, 'order_records' => $order_records];
    }

    public function setSearchConditionalWhereClause($data)
    {
        $search_terms = explode(" ", $data['search_text']);

        if (!isset($data['past_seven_days'])) {
            $data['past_seven_days'] = false;
        }

        if (!$data['past_seven_days']) {
            if (session('user_visibility') == 'global') {
                $this->orders_query = DB::connection('reports_db')->table('smawv_nb_7day_orders as order_view');
            } elseif (session('user_visibility') == 'brand') {
                $this->orders_query = DB::connection('reports_db')->table('smawv_nb_7day_orders as order_view')
                    ->join('Merchant', 'order_view.merchant_id', '=', 'Merchant.merchant_id')
                    ->join('Brand2', 'Merchant.brand_id', '=', 'Brand2.brand_id')
                    ->join('portal_brand_manager_brand_map', 'Merchant.brand_id', '=', 'portal_brand_manager_brand_map.brand_id');
            }
            $viewTableName = 'smawv_nb_7day_orders';
        } else {
            if (session('user_visibility') == 'global') {
                $this->orders_query = DB::connection('reports_db')->table('smawv_nb_orders as order_view');
            } elseif (session('user_visibility') == 'brand') {
                $this->orders_query = DB::connection('reports_db')->table('smawv_nb_orders as order_view')
                    ->join('Merchant', 'order_view.merchant_id', '=', 'Merchant.merchant_id')
                    ->join('Brand2', 'Merchant.brand_id', '=', 'Brand2.brand_id')
                    ->join('portal_brand_manager_brand_map', 'Merchant.brand_id', '=', 'portal_brand_manager_brand_map.brand_id');
            }
            $viewTableName = 'smawv_nb_orders';
        }

        $variable_bindings = [];


        foreach ($search_terms as $search_term) {
            $this->orders_query = $this->orders_query->whereRaw("(order_view.order_id like ? or order_view.merchant_id like ? or order_view.user_id like ? 
                                                    or order_dt_tm like ? or grand_total like ? or order_view.status like ? or
                                                    UPPER(email) like ? or UPPER(promo_code) like ? or UPPER(first_name) like ? or UPPER(last_name) like ? or order_view.address1 like ?
                                                    or UPPER(order_view.order_type) like ? or order_view.name like ?)");

            array_push($variable_bindings, "%" . $search_term . "%", "%" . $search_term . "%", "%" . $search_term . "%", "%" . $search_term . "%", "%" . $search_term . "%",
                "%" . $search_term . "%", "%" . strtoupper($search_term) . "%",
                "%" . strtoupper($search_term) . "%", "%" . strtoupper($search_term) . "%", "%" . strtoupper($search_term) . "%", "%" . $search_term . "%",
                "%" . strtoupper($search_term) . "%", "%" . $search_term . "%");
        }

        if (session('user_visibility') == 'brand') {
            $this->orders_query = $this->orders_query->where('portal_brand_manager_brand_map.user_id', '=', '?');
            $variable_bindings[] = Auth::user()->id;
        }

        $this->orders_query = $this->orders_query->setBindings($variable_bindings);

        $this->orders_query->whereNotIn('order_view.order_id', function ($query) use ($viewTableName) {
            $query->select($viewTableName . '.order_id')
                ->where($viewTableName . '.status', '=', 'Y')
                ->where($viewTableName . '.order_dt_tm', '<', Carbon::now()->subHours(48))
                ->from($viewTableName);
        });

        $this->orders_query = $this->orders_query->leftJoin(DB::raw('(SELECT `Catering_Orders`.`order_id`,\'Catering\' as is_catering FROM Catering_Orders) as Catering_Orders'), 'order_view.order_id', '=', 'Catering_Orders.order_id')
            ->select(['order_view.*', DB::raw('CONCAT_WS(\' \', `is_catering`,`order_view`.`order_type`) as order_type_complete')]);
    }

    public function searchTotalCount()
    {
        $count_query = $this->orders_query;
        return $count_query->count();
    }

    public function getCurrentSearchRecordSet()
    {
        $result = $this->orders_query
            ->skip($this->query_offset)
            ->orderBy($this->order_by)
            ->take(20)
            ->get()
            ->toArray();
        return $result;
    }

    public function searchOffsetResults(Request $request)
    {
        $data = $request->all();

        $this->order_by = $data['order_by'];
        $this->setSearchConditionalWhereClause($data);

        $this->query_offset = ($data['offset'] - 1) * 20;
        $records = $this->getCurrentSearchRecordSet();

        return ['order_records' => $records];
    }

    public function addOrderToRecentlyVisited($new_order)
    {

        if (session('recently_visited_orders')) {
            $recently_visited_orders = session('recently_visited_orders');

            //Remove Merchant if It's Already in Recently Visited
            foreach ($recently_visited_orders as $key => $order) {
                if ($order['order_id'] == $new_order['order_id']) {
                    unset($recently_visited_orders[$key]);
                    break;
                }
            }

            //Remove the Oldest Recently Visited if We're at the Maximum of 5
            if (count($recently_visited_orders) == 5) {
                array_pop($recently_visited_orders);
            }

            //Add the New Merchant to the Beginning of the Array
            array_unshift($recently_visited_orders, $new_order);
        } else {
            $recently_visited_orders = [];
            $recently_visited_orders[] = $new_order;
        }
        session(['recently_visited_orders' => $recently_visited_orders]);
    }

    public function setCurrent(Request $request)
    {
        $data = $request->all();

        $order = Orders::find($data['order_id']);

        session(['current_order' => $order->toArray()]);

        return 1;
    }

    public function liveOrders()
    {
        if (session('user_visibility') == 'global') {
            $live_orders = SmawLiveOrders::take(25)->get()->toArray();
        } elseif (session('user_visibility') == 'brand') {
            $live_orders = $this->orders_query = DB::connection('reports_db')->table('smawv_live_orders as order_view')
                ->join('Merchant', 'order_view.merchant_id', '=', 'Merchant.merchant_id')
                ->where('Merchant.brand_id', '=', session('brand_manager_brand'))
                ->take(25)->get()->toArray();
        }

        $live_orders = array_map(function ($order) {
            $utility = new Utility();

            $order['real_order_id'] = $utility->getTextInBetweenStrings($order['order_id'], '>', '<');
            return $order;
        }, $live_orders);

        return $live_orders;
    }

    public function makePortalOrdersEndpointRequest($function, $data)
    {
        if ($data) {
            Log::info("**************************");
            Log::info("Data for API orders call is as follows:");
            foreach ($data as $name => $value) {
                Log::info("   $name = $value");
            }
            Log::info("**************************");
        } else {
            Log::info("no data passed to pass to curl object");
        }

        $order = session('current_order');
        $order_ucid = $order['ucid'];
        $orders_endpoint_url = "orders/$order_ucid/$function";
        $splickit_admin_curl_service = new SplickitAdminCurlService();
        if ($function == null) {
            $orders_endpoint_url = "orders/$order_ucid";
            $splickit_admin_curl_service->setMethodToGet();
        }
        $response = $splickit_admin_curl_service->makeRequest("$orders_endpoint_url", $data);

        $this->makeAuditRecord($response, $data, $orders_endpoint_url, $function);

        Log::info('CURL RESPONSE: ' . json_encode($response));

        if (isset($response['error'])) {
            Log::info("We have a failure in $function request");
            return $response['error'];
        } else if (isset($response['data']['result']) && $response['data']['result'] == 'success') {
            return $response['data'];
        } else if (isset($response['data']) && $response['data'] != null) {
            return $response['data'];
        } else {
            Log::info("We have a failure in $function request");
            return array("error" => array("error_message" => "There was an unknown error on the request"));
        }
    }

    public function resendOrder(Request $request)
    {
        Log::info("we are starting the resendOrder()");
        $data = $request->all();
        $this->order_event = 'Resend Order';
        return $this->makePortalOrdersEndpointRequest('resendanorder', $data);

    }

    public function reassignOrder(Request $request)
    {
        Log::info("We are starting the reassignOrder()");
        $data = $request->all();
        $this->order_event = 'Reassign Order';
        return $this->makePortalOrdersEndpointRequest('reassignorder', $data);

    }

    public function refundOrder(Request $request)
    {
        Log::info("we are starting the refundOrder()");
        $data = $request->all();
        $this->order_event = 'Refund Order';
        $response = $this->makePortalOrdersEndpointRequest('refundorder', $data);

        if (isset($response['error_message'])) {
            return ['error' => true, 'smaw_response' => $response];
        } else {
            return ['error' => false, 'smaw_response' => $response];
        }
    }

    public function changeOrderStatus(Request $request)
    {
        Log::info("we are starting the changeOrderStatus()");
        $data = $request->all();
        $this->order_event = 'Change Order Status';
        $data['order_status'] = $data['new_status'];
        return $this->makePortalOrdersEndpointRequest('updateorderstatus', $data);
    }

    public function getOrderDetail()
    {
        Log::info("we are starting the changeOrderStatus()");
        return $this->makePortalOrdersEndpointRequest(null, null);
    }

    public function makeAuditRecord($response, $data, $url, $function)
    {
//        $audit = new Audits();
//
//        $audit->new_values = json_encode($data);
//        $audit->old_values = json_encode($response);
//
//        $audit->event = '';
//        $audit->auditable_type = $function;
//
//        $audit->tags = 'smaw_api';
//
//        $audit->auditable_id = session('current_order');
//
//        $audit->url = $url;
//        $audit->user_id = session('user_id');
//
//        $audit->save();
    }
}