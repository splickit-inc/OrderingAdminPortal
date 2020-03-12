<?php
/**
 * Created by PhpStorm.
 * User: diego.rodriguez
 * Date: 12/7/17
 * Time: 11:17 AM
 */

namespace App\Service;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Model\AggregateReports\RptAggregateOrders;
use Illuminate\Support\Facades\Auth;
use App\Model\UserMerchantMap;

class ReportService {
    const CONNECTION = 'reports_db';

    /**
     * Gets the transactions report
     *
     * @param null|string|array $dateRange the date range can be a string
     *                                     separated by commas or an array, null if no
     *                                     date range filter should be applied.
     * @return Builder
     */
    public function transactionsReport($dateRange = null) {
        $orders = DB::connection(ReportService::CONNECTION)->table("rpt_aggregate_orders")
            ->join('Merchant', 'rpt_aggregate_orders.merchant_id', '=', 'Merchant.merchant_id')
            ->select('rpt_aggregate_orders.*',
                DB::raw('DATE(order_date) order_date'),
                DB::raw('HOUR(order_date) order_hour'),
                DB::raw('DAYNAME(order_date) order_day_of_week'),
                DB::raw('MONTHNAME(order_date) order_month'),
                DB::raw('YEAR(order_date) order_year'));
        return $orders;
    }

    /**
     * Gets the customers report
     *
     * @param null|string|array $dateRange the date range can be a string
     *                                     separated by commas or an array, null if no
     *                                     date range filter should be applied.
     * @return Builder
     */
    public function customersReport($request) {
        $dateRange = $request->input('date_range');

        $ordersWithAttributes = $this->getOrdersWithAttributes($dateRange);

        $orders = DB::connection(ReportService::CONNECTION)->table("User")
            ->select('User.user_id', 'first_name', 'last_name', 'email',
                DB::raw('min(order_dt_tm) first_order_date'),
                DB::raw('max(order_dt_tm) last_order_date'),
                DB::raw("(case when order_type = 'D' then 'Delivery' else 'Pick Up' end) order_type"),
                'payment_type', DB::raw('count(1) total_orders'),
                DB::raw("cast(sum(is_pickup_order) as unsigned) pickup_orders"),
                DB::raw("cast(sum(is_delivery_order) as unsigned) delivery_orders"),
                DB::raw("sum(order_amt) order_spent"), DB::raw("sum(grand_total) total_spent"),
                DB::raw("avg(order_amt) avg_order_value"),
                'order_hour', 'order_day_of_week', 'order_month', 'order_year', 'order_date', 'order_dt_tm')
            ->join(DB::raw("(" . $ordersWithAttributes->toSql() . ") as o"), function ($join) {
                $join->on("o.user_id", "=", "User.user_id");
            })
            ->mergeBindings($ordersWithAttributes);

        if (json_decode($request->input('brand')) || session('user_visibility') == 'brand') {
            if (session('user_visibility') == 'brand') {
                $brand = session('brand_manager_brand');
            }
            else {
                $brand = json_decode($request->input('brand'));
            }
            $orders = $orders->join('Merchant', 'o.merchant_id', 'Merchant.merchant_id')
                ->where('Merchant.brand_id', '=', $brand);
        }
        elseif (json_decode(Request()->input('merchants'))) {
            $merchant_list = implode(json_decode(Request()->input('merchants')), ', ');


            $orders = $orders->join('Merchant', 'o.merchant_id', 'Merchant.merchant_id')
                ->whereRaw('Merchant.merchant_id in ('.$merchant_list.')');
        }

        $orders = $orders->groupBy('o.user_id');
        return $orders;
    }

    private function getDateRange($dateRange) {
        if (!isset($dateRange) || is_null($dateRange)) {
            return [null, null];
        }
        $parts = $dateRange;
        if (!is_array($parts)) {
            $parts = explode(',', $dateRange);
        }
        if (count($parts) == 0) {
            return [null, null];
        }
        $from = Carbon::createFromFormat('Y-m-d', trim($parts[0]), 'EST');
        $to = $from;
        if (count($parts) == 2) {
            $to = Carbon::createFromFormat('Y-m-d', trim($parts[1]), 'EST');
        }
        $from->setTime(0, 0);
        $from->setTimezone('UTC');

        $to->setTime(23, 59, 59);
        $to->setTimezone('UTC');

        return [$from, $to];
    }

    /**
     * @param $dateRange
     * @return Builder
     */
    private function getOrdersWithAttributes($dateRange) {
        $ordersWithAttributes = DB::connection(ReportService::CONNECTION)->table('Orders')
            ->select('*', DB::raw("(case when cash = 'Y' then 'Cash' else 'Credit Card' end) payment_type"),
                DB::raw("(case when order_type = 'R' then 1 else 0 end) is_pickup_order"),
                DB::raw("(case when order_type = 'D' then 1 else 0 end) is_delivery_order"),
                DB::raw('DATE(order_dt_tm) order_date'),
                DB::raw('HOUR(order_dt_tm) order_hour'),
                DB::raw('DAYNAME(order_dt_tm) order_day_of_week'),
                DB::raw('MONTHNAME(order_dt_tm) order_month'),
                DB::raw('YEAR(order_dt_tm) order_year'))
            ->where('status', 'E')
            ->whereNotNull('order_dt_tm')
            ->whereRaw('UNIX_TIMESTAMP(order_dt_tm) <> 0');

        if (!is_null($dateRange)) {
            $ordersWithAttributes->whereBetween('order_dt_tm', $this->getDateRange($dateRange));
        }
        return $ordersWithAttributes;
    }
}