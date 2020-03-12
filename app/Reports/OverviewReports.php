<?php namespace App\Reports;

use Illuminate\Support\Facades\DB;

class OverviewReports {
    const CONNECTION = 'reports_db';

    public $request;

    public $start_date;
    public $end_date;

    protected $sales_query;
    protected $sales_variable_bindings = [];

    protected $db_time_periods = [
        'yesterday' => 'Yesterday', 'current_week' => 'This_Week', 'previous_week' => 'Last_Week',
        'current_month' => 'Month_to_Date', 'current_year' => 'Year_to_Date'
    ];

    protected $response;

    public function buildDateRange() {
        if ($this->request['time_range'] == 'current_year') {
            $this->start_date = date('Y') . "-01-01 00:00:00";
            $this->end_date = date('Y-m-d') . " 00:00:00";
        }
        elseif ($this->request['time_range'] == 'today') {
            $current_date = date("Y-m-d");

            $this->start_date = $current_date . " 00:00:00";
            $this->end_date = $current_date . " 23:59:59";
        }
        elseif ($this->request['time_range'] == 'yesterday') {
            $current_date = date("Y-m-d", time() - 60 * 60 * 24);

            $this->start_date = $current_date . " 00:00:00";
            $this->end_date = $current_date . " 23:59:59";
        }
        elseif ($this->request['time_range'] == 'current_week') {
            $this->start_date = date('Y-m-d', strtotime('sunday last week'));
            $this->end_date = date('Y-m-d', strtotime('saturday this week'));
        }
        elseif ($this->request['time_range'] == 'previous_week') {
            $this->start_date = date('Y-m-d', strtotime('-2 weeks sunday'));
            $this->end_date = date('Y-m-d', strtotime('sunday this week'));
        }
        elseif ($this->request['time_range'] == 'current_month') {
            $this->start_date = date('Y-m-01') . " 00:00:00";
            $this->end_date = date('Y-m-d') . " 00:00:00";
        }
    }

    public function getReportData() {
        $this->buildDateRange();
        $this->salesData();
        $this->orderSummary();
        $this->dayOfWeekSales();
        $this->pickupVsDelivery();
        $this->topMenuItems();
        $this->topUsers();
        $this->deviceData();

        return $this->response;
    }

    protected function dayOfWeekSales() {

        if ($this->request['time_range'] == 'today') {

            $this->sales_query = DB::connection(OverviewReports::CONNECTION)->table("Orders")
                ->join('Merchant', 'Orders.merchant_id', '=', 'Merchant.merchant_id');

            $this->sales_query = $this->sales_query->select(
                DB::raw('DAYNAME(order_dt_tm) as day_name'),
                DB::raw('SUM(order_amt) as daily_sales'),
                DB::raw('DAYOFWEEK(order_dt_tm) as day_of_week')
            );

            $date_column = 'order_dt_tm';

            $this->sales_query = $this->addMerchantBrandCriteria($this->sales_query);
            $this->sales_query = $this->addSalesDateRange($this->sales_query, $date_column);

            $this->sales_query = $this->sales_query->groupBy('day_name');
            $this->sales_query = $this->sales_query->orderBy('day_of_week');

            $data = $this->sales_query->get()->toArray();
        }
        else {
            $this->sales_query = DB::connection(OverviewReports::CONNECTION)->table("rpt_aggregate_orders")
                ->join('Merchant', 'rpt_aggregate_orders.merchant_id', '=', 'Merchant.merchant_id');

            $this->sales_query = $this->sales_query->select(
                DB::raw('DAYNAME(order_date) as day_name'),
                DB::raw('SUM(order_amt) as daily_sales'),
                DB::raw('DAYOFWEEK(order_date) as day_of_week')
            );

            $this->sales_query = $this->addMerchantBrandCriteria($this->sales_query);
            $this->sales_query = $this->addSalesDateRange($this->sales_query);

            $this->sales_query = $this->sales_query->groupBy('day_name');
            $this->sales_query = $this->sales_query->orderBy('day_of_week');

            $data = $this->sales_query->get()->toArray();
        }



        $day_of_week_sales = [
            'labels' => [],
            'sales' => []
        ];

        foreach ($data as $day) {
            $day_of_week_sales['labels'][] = $day->day_name;
            $day_of_week_sales['sales'][] = $day->daily_sales;
        }

        $this->response['day_of_week'] = $day_of_week_sales;
    }

    protected function getMetricBaseQuery() {
        $metric_base_query = DB::connection(OverviewReports::CONNECTION)->table("rpt_aggregate_top_metrics_by_merchant");

        if (isset($this->request['merchant'])) {
            $metric_base_query = $metric_base_query->where('merchant_id', '=', $this->request['merchant']);
        }
        elseif (isset($this->request['brand_id'])) {
            $metric_base_query = $metric_base_query->where('brand_id', '=', $this->request['brand_id']);
        }
        elseif (isset($this->request['merchants'])) {
            $metric_base_query = $metric_base_query->whereIn('merchant_id', $this->request['merchants']);
        }

        $metric_base_query = $metric_base_query->where('period', '=', $this->db_time_periods[$this->request['time_range']]);

        return $metric_base_query;
    }

    protected function orderSummary()
    {
        $this->response['summary_secondary'] = [];

        if ($this->request['time_range'] == 'today') {

            $this->sales_query = DB::connection(OverviewReports::CONNECTION)->table("Orders")
                ->join('Merchant', 'Orders.merchant_id', '=', 'Merchant.merchant_id');

            $this->sales_query = $this->sales_query->select(
                DB::raw('COUNT(order_id) as order_count'),
                DB::raw('SUM(order_amt) as order_total'),
                DB::raw('SUM(total_tax_amt) as tax_total'),
                DB::raw('SUM(tip_amt) as total_tip'),
                DB::raw('SUM(promo_amt) as promo_total'),
                DB::raw('SUM(delivery_amt) as delivery_total'),
                DB::raw('SUM(grand_total) as total_grand_total')
            );
            $date_column = 'order_dt_tm';
        } else {
            $this->sales_query = DB::connection(OverviewReports::CONNECTION)->table("rpt_aggregate_orders")
                ->join('Merchant', 'rpt_aggregate_orders.merchant_id', '=', 'Merchant.merchant_id');

            $this->sales_query = $this->sales_query->select(
                DB::raw('SUM(order_cnt) as order_count'),
                DB::raw('SUM(order_amt) as order_total'),
                DB::raw('SUM(promo_amt) as promo_total')
            );
            $date_column = 'order_date';

            $catering_sales_query = $this->getMetricBaseQuery();

            $catering_sales_query = $catering_sales_query->where('type', '=', 'Catering_Sales')->select(
                DB::raw('SUM(orders) as order_count'),
                DB::raw('SUM(sales) as sales'));

            $data = $catering_sales_query->get()->toArray();

            $this->response['summary_secondary']['catering'] = $data[0];
            $this->response['summary_secondary']['catering']->sales = number_format($this->response['summary_secondary']['catering']->sales);

            $promo_sales_query = $this->getMetricBaseQuery();

            $promo_sales_query = $promo_sales_query->where('type', '=', 'Promo_Sales')->select(
                DB::raw('SUM(orders) as order_count'),
                DB::raw('SUM(sales) as sales'));

            $data = $promo_sales_query->get()->toArray();

            $this->response['summary_secondary']['promos'] = $data[0];
            $this->response['summary_secondary']['promos']->sales = number_format($this->response['summary_secondary']['promos']->sales);

            $guest_sales_query = $this->getMetricBaseQuery();

            $guest_sales_query = $guest_sales_query->where('type', '=', 'Guest_Sales')->select(
                DB::raw('SUM(orders) as order_count'),
                DB::raw('SUM(sales) as sales'));

            $data = $guest_sales_query->get()->toArray();

            $this->response['summary_secondary']['guest'] = $data[0];
            $this->response['summary_secondary']['guest']->sales = number_format($this->response['summary_secondary']['guest']->sales);

            $group_sales_query = $this->getMetricBaseQuery();

            $group_sales_query = $group_sales_query->where('type', '=', 'Group_Sales')->select(
                DB::raw('SUM(orders) as order_count'),
                DB::raw('SUM(sales) as sales'));

            $data = $group_sales_query->get()->toArray();

            $this->response['summary_secondary']['group'] = $data[0];
            $this->response['summary_secondary']['group']->sales = number_format($this->response['summary_secondary']['group']->sales);

        }

        $this->sales_query = $this->addMerchantBrandCriteria($this->sales_query);
        $this->sales_query = $this->addSalesDateRange($this->sales_query, $date_column);

        $data = $this->sales_query->first();

        if ($data->order_count > 0) {
            $data->order_average = round($data->order_total/$data->order_count, 2);
        }
        else {
            $data->order_average = 0;
        }

        $this->response['summary'] = $data;
        $this->response['summary']->order_total = number_format($this->response['summary']->order_total);
        $this->response['summary']->order_count = number_format($this->response['summary']->order_count);

    }

    public function pickupVsDelivery() {
        if ($this->request['time_range'] == 'today') {
            $data = new \stdClass();

            $this->sales_query = DB::connection(OverviewReports::CONNECTION)->table("Orders")
                ->join('Merchant', 'Orders.merchant_id', '=', 'Merchant.merchant_id');

            $this->sales_query = $this->addMerchantBrandCriteria($this->sales_query);
            $this->sales_query = $this->addSalesDateRange($this->sales_query, 'order_dt_tm');
            $data->pickup_count = $this->sales_query->where('order_type', '=', 'R')->count();
            $data->delivery_count = $this->sales_query->where('order_type', '=', 'D')->count();
        }
        else {
            $this->sales_query = DB::connection(OverviewReports::CONNECTION)->table("rpt_aggregate_orders")
                ->join('Merchant', 'rpt_aggregate_orders.merchant_id', '=', 'Merchant.merchant_id');

            $this->sales_query = $this->sales_query->select(
                DB::raw('SUM(is_pickuup_order) as pickup_count'),
                DB::raw('SUM(is_delivery_order) as delivery_count')
            );

            $this->sales_query = $this->addMerchantBrandCriteria($this->sales_query);
            $this->sales_query = $this->addSalesDateRange($this->sales_query);

            $data = $this->sales_query->first();
        }


        $this->response['pickup_delivery'] = [
            'labels' => ['Pickup', 'Delivery'],
            'chart_values' => [$data->pickup_count, $data->delivery_count]
        ];
    }

    public function topMenuItems() {

        if ($this->request['time_range'] == 'today') {
            $this->sales_query = DB::connection(OverviewReports::CONNECTION)->table("Orders")
                ->join('Order_Detail', 'Orders.order_id', '=', 'Order_Detail.order_id')
                ->join('Merchant', 'Orders.merchant_id', '=', 'Merchant.merchant_id')
                ;

            $this->sales_query = $this->sales_query->select(
                DB::raw('Orders.merchant_id, Orders.order_dt_tm, Order_Detail.item_name,
                        sum(Order_Detail.item_total) as item_sales')
            );

            $this->sales_query = $this->sales_query->where('Orders.status', '=', 'E');

            $date_variable = 'Orders.order_dt_tm';

            $this->sales_query = $this->addMerchantBrandCriteria($this->sales_query);
            $this->sales_query = $this->addSalesDateRange($this->sales_query, $date_variable);

            $this->sales_query = $this->sales_query->groupBy('Orders.merchant_id', 'Orders.order_dt_tm', 'Order_Detail.item_name');
            $this->sales_query = $this->sales_query->orderBy('item_sales', 'desc');
            $this->sales_query->take(5);

            $data = $this->sales_query->get();

            $this->response['item_sales'] = $data;
        }
        else {
            $this->sales_query = DB::connection(OverviewReports::CONNECTION)->table("rpt_aggregate_menu_type")
                ->join('Merchant', 'rpt_aggregate_menu_type.merchant_id', '=', 'Merchant.merchant_id');

            $this->sales_query = $this->sales_query->select(
                DB::raw('SUM(Item_Total) as item_sales'),
                DB::raw('concat(rpt_aggregate_menu_type.Item_Name, " ", rpt_aggregate_menu_type.Size_Name) as item_name'),
                'item_size_id'
            );

            $date_variable = 'rpt_aggregate_menu_type.Date';

            $group_by_item_size_id_col = 'item_name';

            $this->sales_query = $this->addMerchantBrandCriteria($this->sales_query);
            $this->sales_query = $this->addSalesDateRange($this->sales_query, $date_variable);

            $this->sales_query = $this->sales_query->groupBy($group_by_item_size_id_col);
            $this->sales_query = $this->sales_query->orderBy('item_sales', 'desc');
            $this->sales_query->take(5);

            $data = $this->sales_query->get();

            $this->response['item_sales'] = $data;
        }
    }

    public function topUsers() {

        if ($this->request['time_range'] == 'today') {

            $query = DB::connection(OverviewReports::CONNECTION)->table("User")
                ->join('Orders', 'User.user_id', '=', 'Orders.user_id')
                ->join('Merchant', 'Orders.merchant_id', '=', 'Merchant.merchant_id');

            $query = $this->addMerchantBrandCriteria($query);

            $query = $query->where('Orders.order_dt_tm', '>=', $this->start_date)
                           ->where('Orders.order_dt_tm', '<=', $this->end_date)
                           ->where('Orders.status', '=', 'E');

            $query = $query->select(
                DB::raw("concat(first_name, ' ', last_name) as full_name"),
                DB::raw('SUM(Orders.order_amt) as user_sales'),
                DB::raw('User.user_id')
            );

            $query = $query->groupBy('User.user_id');
            $query = $query->orderBy('user_sales', 'desc');

            $this->response['user_sales'] = $query->take(5)->get()->toArray();
        }
        else {
            $this->sales_query = DB::connection(OverviewReports::CONNECTION)->table("rpt_aggregate_top_sales_by_user");

            $this->sales_query = $this->sales_query->select(
                DB::raw('SUM(sales) as user_sales'),
                'user_id', 'full_name', 'user_id'
            );

            if (isset($this->request['merchant'])) {
                $this->sales_query = $this->sales_query->where('rpt_aggregate_top_sales_by_user.merchant_id', '=', $this->request['merchant']);
            }
            elseif (isset($this->request['brand_id'])) {
                $this->sales_query = $this->sales_query->where('rpt_aggregate_top_sales_by_user.brand_id', '=', $this->request['brand_id']);
            }
            elseif (isset($this->request['merchants'])) {
                $this->sales_query = $this->sales_query->whereIn('rpt_aggregate_top_sales_by_user.merchant_id', $this->request['merchants']);
            }

            $this->sales_query = $this->sales_query->where('period', '=', $this->db_time_periods[$this->request['time_range']]);

            $this->sales_query = $this->sales_query->groupBy('user_id');
            $this->sales_query = $this->sales_query->orderBy('user_sales', 'desc');

            DB::connection(OverviewReports::CONNECTION)->enableQueryLog();
            $this->response['user_sales'] = $this->sales_query->take(5)->get()->toArray();
            $q = DB::connection(OverviewReports::CONNECTION)->enableQueryLog();
        }
    }

    protected function deviceData() {
        if ($this->request['time_range'] == 'today') {
            $query = DB::connection(OverviewReports::CONNECTION)->table("Orders")
                ->join('Merchant', 'Orders.merchant_id', '=', 'Merchant.merchant_id');

            $query = $query->select(
                DB::raw("device_type"),
                DB::raw('sum(order_amt) as sales')
            );

            $query = $this->addMerchantBrandCriteria($query);

            $query = $query->where('Orders.order_dt_tm', '>=', $this->start_date)
                ->where('Orders.order_dt_tm', '<=', $this->end_date)
                ->where('Orders.status', '=', 'E');

            $query = $query->groupBy('device_type');

            $data = $query->get()->toArray();

            $this->response['device_sales'] = [
                'labels' => [],
                'sales' => []
            ];

            foreach ($data as $device_data) {
                $this->response['device_sales']['labels'][] = $device_data->device_type;
                $this->response['device_sales']['sales'][] = $device_data->sales;
            }
        }
        else {
            $this->sales_query = DB::connection(OverviewReports::CONNECTION)->table("rpt_aggregate_orders")
                ->join('Merchant', 'rpt_aggregate_orders.merchant_id', '=', 'Merchant.merchant_id');

            $this->sales_query = $this->sales_query->select(
                DB::raw('SUM(android) as android_count'),
                DB::raw('SUM(iphone) as iphone_count'),
                DB::raw('SUM(web) as web_count'),
                DB::raw('SUM(web_mobile) as web_mobile_count')
            );

            $this->sales_query = $this->addMerchantBrandCriteria($this->sales_query);
            $this->sales_query = $this->addSalesDateRange($this->sales_query);

            $data = $this->sales_query->get()->toArray();


            $this->response['device_sales'] = ['labels'=>[
                'Android',
                'Iphone',
                'Web',
                'Web Mobile'
            ], 'sales'=>[
                $data[0]->android_count,
                $data[0]->iphone_count,
                $data[0]->web_count,
                $data[0]->web_mobile_count,
            ]];
        }


    }

    public function salesData() {
        $this->sales_query = DB::connection(OverviewReports::CONNECTION)->table("rpt_aggregate_orders")
            ->join('Merchant', 'rpt_aggregate_orders.merchant_id', '=', 'Merchant.merchant_id');

        $date_column = 'order_date';

        if ($this->request['time_range'] == 'current_year') {
            $this->sales_query = $this->sales_query->select(
                DB::raw('MONTHNAME(order_date) as order_month'),
                DB::raw('SUM(order_amt) as monthly_sales'),
                DB::raw('SUM(order_cnt) as order_count')
            );
            $group_by = 'order_month';
        }
        elseif ($this->request['time_range'] == 'current_year') {
            $this->sales_query = $this->sales_query->select(
                DB::raw('MONTHNAME(order_date) as order_month'),
                DB::raw('SUM(order_amt) as monthly_sales'),
                DB::raw('SUM(order_cnt) as order_count')
            );

            $group_by = 'order_month';
        }
        elseif ($this->request['time_range'] == 'current_month') {

            $this->sales_query = $this->sales_query->select(
                DB::raw('WEEK(order_date) as order_week'),
                DB::raw('SUM(order_amt) as monthly_sales'),
                DB::raw('SUM(order_cnt) as order_count')
            );

            $group_by = 'order_week';
        }
        elseif ($this->request['time_range'] == 'previous_week' || $this->request['time_range'] == 'current_week') {

            $this->sales_query = $this->sales_query->select(
                DB::raw('DATE_FORMAT(order_date, "%m/%d") as order_day'),
                DB::raw('SUM(order_amt) as monthly_sales'),
                DB::raw('SUM(order_cnt) as order_count')
            );

            $group_by = 'order_day';
        }
        elseif ($this->request['time_range'] == 'previous_week' || $this->request['time_range'] == 'current_week') {

            $this->sales_query = $this->sales_query->select(
                DB::raw('DATE_FORMAT(order_date, "%m/%d") as order_day'),
                DB::raw('SUM(order_amt) as monthly_sales'),
                DB::raw('SUM(order_cnt) as order_count')
            );

            $group_by = 'order_day';
        }
        elseif ($this->request['time_range'] == 'yesterday') {

            $this->sales_query = $this->sales_query->select(
                DB::raw("CASE
                        when order_hour = 0 then '12am'
                        WHEN order_hour > 12 THEN concat(order_hour-12, 'pm')
                        ELSE concat(order_hour, 'am')
                        END as order_hour "),
                DB::raw('SUM(order_amt) as monthly_sales'),
                DB::raw('SUM(order_cnt) as order_count')
            );

            $group_by = 'order_hour';
        }
        else {
            $this->sales_query = DB::connection(OverviewReports::CONNECTION)->table("Orders")
                ->join('Merchant', 'Orders.merchant_id', '=', 'Merchant.merchant_id');

            $this->sales_query = $this->sales_query->select(
                DB::raw("HOUR(order_dt_tm) as order_day"),
                DB::raw('sum(order_amt) as monthly_sales'),
                DB::raw('count(order_id) as order_count')
            );
            $date_column = 'order_dt_tm';
            $group_by = 'order_day';
        }

        $this->sales_query = $this->addMerchantBrandCriteria($this->sales_query);
        $this->sales_query = $this->addSalesDateRange($this->sales_query, $date_column);

        $this->sales_query->groupBy($group_by);

        $data = $this->sales_query->get()->toArray();

        $response = $this->buildLabelsChartData($data, $group_by);

        $this->response['labels'] = $response['labels'];
        $this->response['chart_values'] = $response['chart_values'];
    }

    public function buildLabelsChartData($data, $group_by) {
        $labels = [];
        $chart_data = [];
        foreach ($data as $data_set) {
            $labels[] = $data_set->{$group_by};

            $chart_data[0][] = $data_set->monthly_sales;
            $chart_data[1][] = $data_set->order_count;
        }
        return ['labels'=>$labels, 'chart_values'=>$chart_data, 'pickup_delivery_labels'=>['Pickup', 'Delivery'], 'pickup_delivery_data'=>[]];
    }

    public function addMerchantBrandCriteria($query) {
        if (isset($this->request['merchant'])) {
            $query->where('Merchant.merchant_id', '=', $this->request['merchant']);
        }
        elseif (isset($this->request['brand_id'])) {
            $query->where('Merchant.brand_id', '=', $this->request['brand_id']);
        }
        elseif (isset($this->request['merchants'])) {
            $query->whereIn('Merchant.merchant_id', $this->request['merchants']);
        }
        return $query;
    }

    protected function addSalesDateRange($query, $table_date_column = 'order_date') {
        $query = $query->where($table_date_column, '>=', $this->start_date)
            ->where($table_date_column, '<', $this->end_date);
        return $query;
    }
}