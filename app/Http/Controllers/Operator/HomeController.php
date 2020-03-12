<?php namespace App\Http\Controllers\Operator;

use App\Http\Controllers\SplickitApiCurlController;
use App\Service\SplickitAPICurlService;
use \Request;
use Illuminate\Support\Facades\Auth;
use \App\Http\Controllers\Controller;
use \App\Model\Orders;
use \DB;
use App\Service\ChartService;

class HomeController extends SplickitApiCurlController {

    public function __construct() {
        $this->chart_service = new ChartService();
    }

    public function index() {

        $one_week_ago_seconds = time() - (24*60*60*7);
        $one_week_ago_day = date("Y-m-d", $one_week_ago_seconds)." 00:00:00";

        $order_data = DB::connection('reports_db')->select('select DATE_FORMAT(order_dt_tm, \'%Y-%m-%d\') as date_day, count(*) as order_count, sum(order_qty) as item_count, sum(order_amt) as order_total,  
            sum(total_tax_amt) as tax_total, sum(tip_amt) as total_tip, sum(promo_amt) as promo_total, sum(delivery_amt) as delivery_total, sum(grand_total) as total_grand_total 
             from Orders 
             where merchant_id = ? and (status = "E" or status = "O") and order_dt_tm >= "'.$one_week_ago_day.'"
             group by date_day;', [session('current_merchant_id')]);

        date_default_timezone_set('America/Chicago');

        $today_start = date("Y-m-d")." 00:00:00";
        $today_end = date("Y-m-d")." 23:59:59";

        //Current Day Data
        $current_day_data = DB::connection('reports_db')->select('select count(*) as order_count, sum(order_qty) as item_count, sum(order_amt) as order_total,  
            avg(order_amt) as order_average , sum(promo_amt) as promo_total
             from Orders 
             where merchant_id = ? and order_dt_tm >= ? and  order_dt_tm <= ? and (status = "E" or status = "O")', [session('current_merchant_id'), $today_start, $today_end]);

        $current_day_data[0]->order_average = round($current_day_data[0]->order_average, 2);
        $current_day_data[0]->order_total = round($current_day_data[0]->order_total, 2);
        $current_day_data[0]->promo_total = round($current_day_data[0]->promo_total, 2);

        //Device Type
        $response = DB::connection('reports_db')->select("select device_type, count(*) as device_count from Orders 
             where merchant_id = ? and order_dt_tm > ? and  order_dt_tm < ? group by device_type;",
            [session('current_merchant_id'), $today_start, $today_end]);

        $device_types = $this->chart_service->convertCountsToChartLabelsAndData($response, 'device_type', 'device_count');

        $sunday_date = date("Y-m-d", strtotime('sunday last week'));

        $this_week_daily_sales = DB::connection('reports_db')->select('select DATE_FORMAT(order_dt_tm, \'%Y-%m-%d\') as date_day, sum(order_amt) as order_total 
             from smawv_nb_7day_orders 
             where merchant_id = ? and order_dt_tm >= "'.$sunday_date.'" 
             group by date_day;', [session('current_merchant_id')]);

        $week_day_db_sales = [];
        foreach ($this_week_daily_sales as $day_date) {
            $week_day_db_sales[$day_date->date_day] = $day_date->order_total;
        }

        $current_day = $sunday_date;

        $today = date("Y-m-d", time());
        $week_day_sales = [];
        $week_day_sales['labels'] = [];
        $week_day_sales['data'] = [];

        while ($current_day <= $today) {
            if (isset($week_day_db_sales[$current_day])) {
                $week_day_sales['labels'][] = date('l', strtotime($current_day));
                $week_day_sales['data'][] = $week_day_db_sales[$current_day];
            }
            else {
                $week_day_sales['labels'][] = date('l', strtotime($current_day));
                $week_day_sales['data'][] = 0;
            }
            $current_day = date('Y-m-d', strtotime($current_day . ' +1 day'));
        }
        return ['weekday_sales'=>$week_day_sales, 'daily_data'=>$order_data, 'current_day_data'=>$current_day_data[0], 'device_types'=>$device_types];
    }

    public function dailySummaryDay() {
        $data = Request::all();

        $day_order_data = DB::connection('reports_db')->select('select order_id, DATE_FORMAT(pickup_dt_tm, \'%h:%i %p\') as pickup_time, first_name, last_name, phone_no, order_qty, tip_amt, grand_total, note , user_id, refunded.refund_id, refunded.refund_order_id, refunded.refund_note
	        from smawv_nb_orders 
	        LEFT JOIN (select id as refund_id, order_id as refund_order_id, note as refund_note from adm_order_reversal) refunded on smawv_nb_orders.order_id = refunded.refund_order_id
             where merchant_id = ? and DATE_FORMAT(order_dt_tm, \'%Y-%m-%d\') = ? and (smawv_nb_orders.status = "E" or smawv_nb_orders.status = "O")
             order by order_id', [session('current_merchant_id'), $data['day_date']]);

        return $day_order_data;
    }

    public function setRefundOrder() {
        $data = Request::all();

        $order = Orders::find($data['order_id'])->toArray();

        session(['current_order'=>$order]);
    }

    public function getMultiOperatorMerchants() {
        $query = DB::table('Merchant')
            ->join('portal_operator_merchant_map', 'Merchant.merchant_id', '=', 'portal_operator_merchant_map.merchant_id');

        $query = $query->whereRaw("(portal_operator_merchant_map.user_id = ?) and (logical_delete = 'N')");

        $query->setBindings([Auth::user()->id]);

        $query->orderBy('Merchant.name', 'Merchant.state', 'Merchant.city', 'Merchant.address1');

        return $query->get(['name', 'Merchant.merchant_id', 'name', 'address1', 'city', 'state', 'phone_no']);
    }
}