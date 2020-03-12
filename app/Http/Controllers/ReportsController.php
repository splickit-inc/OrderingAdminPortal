<?php namespace App\Http\Controllers;

use App\Model\ReportSchedules;
use \DB;
use \App\Model\Orders;
use \App\Service\Utility;
use Illuminate\Http\Request;
use App\Reports\OverviewReports;
use App\Model\ObjectOwnership;
use \Illuminate\Support\Facades\Auth;

class ReportsController extends SplickitApiCurlController
{

    public $filterType;
    public $data;

    public function __construct()
    {
        date_default_timezone_set('America/Denver');
        parent::__construct();
    }

    public function setFilterType()
    {
        if (isset($this->data['brand_id'])) {
            $this->filterType = 'brand';
        } elseif (isset($this->data['merchants'])) {
            $this->filterType = 'multiple_merchants';
            $this->data['merchants'] = implode($this->data['merchants'], ', ');
        } else {
            $this->filterType = 'single_merchant';
            $this->data['merchant'] = session('current_merchant_id');
        }
    }

    public function todayReportData(Request $request, Orders $model)
    {
        $current_date = date("Y-m-d");

        $date_range_start = $current_date . " 00:00:00";
        $date_range_end = $current_date . " 23:59:59";

        $this->data = $request->all();
        $result = $model->getOrdersSummary($this->data, $date_range_start, $date_range_end);
        $this->setFilterType();

        $daily_report_data = $this->dailyReportData($current_date);
        $day_of_week = $this->getSalesByWeekday($date_range_start, $date_range_end);

        $user_sales = $this->userSalesLeaders($date_range_start, $date_range_end);
        $item_sales = $this->itemSalesLeaders($date_range_start, $date_range_end);
        $device_sales = $this->deviceTypeSales($date_range_start, $date_range_end);
        $pickup_delivery = $this->pickupVsDelivery($date_range_start, $date_range_end);

        return ['labels' => $daily_report_data['hours'], 'chart_values' => $daily_report_data['sales'],
            'user_sales' => $user_sales, 'item_sales' => $item_sales, 'device_sales' => $device_sales, 'pickup_delivery' => $pickup_delivery, 'day_of_week' => $day_of_week, 'summary' => $result];
    }

    public function yesterdayReportData(Request $request, Orders $model)
    {
        $current_date = date("Y-m-d", time() - 60 * 60 * 24);

        $date_range_start = $current_date . " 00:00:00";
        $date_range_end = $current_date . " 23:59:59";

        $this->data = $request->all();
        $result = $model->getOrdersSummary($this->data, $date_range_start, $date_range_end);
        $this->setFilterType();

        $daily_report_data = $this->dailyReportData($current_date);
        $day_of_week = $this->getSalesByWeekday($date_range_start, $date_range_end);

        $user_sales = $this->userSalesLeaders($date_range_start, $date_range_end);
        $item_sales = $this->itemSalesLeaders($date_range_start, $date_range_end);
        $device_sales = $this->deviceTypeSales($date_range_start, $date_range_end);
        $pickup_delivery = $this->pickupVsDelivery($date_range_start, $date_range_end);

        return ['labels' => $daily_report_data['hours'], 'chart_values' => $daily_report_data['sales'],
            'user_sales' => $user_sales, 'item_sales' => $item_sales, 'device_sales' => $device_sales, 'pickup_delivery' => $pickup_delivery, 'day_of_week' => $day_of_week, 'summary' => $result];
    }

    public function dailyReportData($day)
    {
        $utility = new Utility();

        $date_range_start = $day . " 00:00:00";
        $date_range_end = $day . " 23:59:59";

        if ($this->filterType == 'single_merchant') {
            $data = DB::connection('reports_db')->select("select HOUR(order_dt_tm) as order_hour, sum(order_amt) as hour_sales, count(order_id) as order_count from Orders
                    where (order_dt_tm >= :date_range_start and order_dt_tm <= :date_range_end) and merchant_id = :merchant_id and Orders.status = 'E' group by order_hour",

                ["date_range_start" => $date_range_start, "date_range_end" => $date_range_end, "merchant_id" => $this->data['merchant']]);
        } elseif ($this->filterType == 'brand') {
            $data = DB::connection('reports_db')->select("select HOUR(order_dt_tm) as order_hour, sum(order_amt) as hour_sales, count(order_id) as order_count from Orders
                    join Merchant on Orders.merchant_id = Merchant.merchant_id 
                    where (order_dt_tm >= :date_range_start and order_dt_tm <= :date_range_end) and Merchant.brand_id = :brand_id and Orders.status = 'E' group by order_hour",

                ["date_range_start" => $date_range_start, "date_range_end" => $date_range_end, "brand_id" => $this->data['brand_id']]);
        } elseif ($this->filterType == 'multiple_merchants') {
            $data = DB::connection('reports_db')->select("select HOUR(order_dt_tm) as order_hour, sum(order_amt) as hour_sales, count(order_id) as order_count from Orders
                    where (order_dt_tm >= :date_range_start and order_dt_tm <= :date_range_end) and merchant_id in  ( :merchants ) and Orders.status = 'E' group by order_hour",

                ["date_range_start" => $date_range_start, "date_range_end" => $date_range_end, "merchants" => $this->data['merchants']]);
        }


        $first_hour = $utility->minAttributeInArrayOfObjects($data, 'order_hour');
        $last_hour = $utility->maxAttributeInArrayOfObjects($data, 'order_hour');

        $current_hour = $first_hour;

        $sales = [];
        $order_counts = [];
        $hours = [];

        foreach ($data as $hour) {
            $sales[$hour->order_hour] = $hour->hour_sales;
            $order_counts[$hour->order_hour] = $hour->order_count;
        }

        $sales_values = [];
        $order_values = [];

        while ($current_hour <= $last_hour) {
            if (isset($sales[$current_hour])) {
                $hours[] = $utility->convert24HourToAmPmValue($current_hour);
                $sales_values[] = (float)$sales[$current_hour];
                $order_values[] = (int)$order_counts[$current_hour];
            } else {
                $hours[] = $utility->convert24HourToAmPmValue($current_hour);
                $sales_values[] = 0;
                $order_values[] = 0;
            }
            $current_hour++;
        }

        $data_sets = [];
        $data_sets[] = $sales_values;
        $data_sets[] = $order_values;
        return ['hours' => $hours, 'sales' => $data_sets];
    }

    public function currentWeekData(Request $request, Orders $model)
    {
        $date_range_start = date('Y-m-d', strtotime('sunday last week'));
        $date_range_end = date('Y-m-d', strtotime('saturday this week'));

        $this->data = $request->all();
        $result = $model->getOrdersSummary($this->data, $date_range_start, $date_range_end);
        $this->setFilterType();

        $weekly_report_data = $this->weeklyReportData($date_range_start, $date_range_end);
        $day_of_week = $this->getSalesByWeekday($date_range_start, $date_range_end);

        $user_sales = $this->userSalesLeaders($date_range_start, $date_range_end);
        $item_sales = $this->itemSalesLeaders($date_range_start, $date_range_end);
        $device_sales = $this->deviceTypeSales($date_range_start, $date_range_end);
        $pickup_delivery = $this->pickupVsDelivery($date_range_start, $date_range_end);

        return ['labels' => $weekly_report_data['labels'], 'chart_values' => $weekly_report_data['data'],
            'user_sales' => $user_sales, 'item_sales' => $item_sales, 'device_sales' => $device_sales, 'pickup_delivery' => $pickup_delivery, 'day_of_week' => $day_of_week, 'summary' => $result];
    }

    public function previousWeekData(Request $request, Orders $model)
    {
        $date_range_start = date('Y-m-d', strtotime('-2 weeks sunday'));
        $date_range_end = date('Y-m-d', strtotime('sunday this week'));

        $this->data = $request->all();
        $result = $model->getOrdersSummary($this->data, $date_range_start, $date_range_end);
        $this->setFilterType();

        $weekly_report_data = $this->weeklyReportData($date_range_start, $date_range_end);
        $day_of_week = $this->getSalesByWeekday($date_range_start, $date_range_end);

        $user_sales = $this->userSalesLeaders($date_range_start, $date_range_end);
        $item_sales = $this->itemSalesLeaders($date_range_start, $date_range_end);
        $device_sales = $this->deviceTypeSales($date_range_start, $date_range_end);
        $pickup_delivery = $this->pickupVsDelivery($date_range_start, $date_range_end);

        return ['labels' => $weekly_report_data['labels'], 'chart_values' => $weekly_report_data['data'],
            'user_sales' => $user_sales, 'item_sales' => $item_sales, 'device_sales' => $device_sales, 'pickup_delivery' => $pickup_delivery, 'day_of_week' => $day_of_week, 'summary' => $result];
    }

    public function weeklyReportData($date_range_start, $date_range_end)
    {

        if ($this->filterType == 'single_merchant') {
            $response = DB::connection('reports_db')->select("select DATE_FORMAT(order_dt_tm, '%m/%d') as order_day, sum(order_amt) daily_sales, count(order_id) as order_count from Orders 
                  where (order_dt_tm >= :date_range_start and order_dt_tm <= :date_range_end) and merchant_id = :merchant_id and Orders.status = 'E' group by order_day order by order_day;",

                ["date_range_start" => $date_range_start, "date_range_end" => $date_range_end, "merchant_id" => $this->data['merchant']]);
        } elseif ($this->filterType == 'brand') {
            $response = DB::connection('reports_db')->select("select DATE_FORMAT(order_dt_tm, '%m/%d') as order_day, sum(order_amt) daily_sales, count(order_id) as order_count from Orders 
                  join Merchant on Orders.merchant_id = Merchant.merchant_id 
                  where (order_dt_tm >= :date_range_start and order_dt_tm <= :date_range_end) and Merchant.brand_id = :brand_id  and Orders.status = 'E' group by order_day order by order_day;",

                ["date_range_start" => $date_range_start, "date_range_end" => $date_range_end, "brand_id" => $this->data['brand_id']]);
        } elseif ($this->filterType == 'multiple_merchants') {
            $response = DB::connection('reports_db')->select("select DATE_FORMAT(order_dt_tm, '%m/%d') as order_day, sum(order_amt) daily_sales, count(order_id) as order_count from Orders 
                  where (order_dt_tm >= :date_range_start and order_dt_tm <= :date_range_end) and merchant_id in  ( :merchants ) and Orders.status = 'E' group by order_day order by order_day;",

                ["date_range_start" => $date_range_start, "date_range_end" => $date_range_end, "merchants" => $this->data['merchants']]);
        }

        $labels = [];
        $data = [];

        foreach ($response as $day) {
            $labels[] = $day->order_day;

            $data[0][] = $day->daily_sales;
            $data[1][] = $day->order_count;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public function currentMonthData(Request $request, Orders $model)
    {

        $date_range_start = date('Y-m-01') . " 00:00:00";
        $date_range_end = date('Y-m-d') . " 00:00:00";

        $this->data = $request->all();
        $result = $model->getOrdersSummary($this->data, $date_range_start, $date_range_end);
        $this->setFilterType();

        $month_data = $this->monthData($date_range_start);
        $day_of_week = $this->getSalesByWeekday($date_range_start, $date_range_end);

        $user_sales = $this->userSalesLeaders($date_range_start, $date_range_end);
        $item_sales = $this->itemSalesLeaders($date_range_start, $date_range_end);
        $device_sales = $this->deviceTypeSales($date_range_start, $date_range_end);
        $pickup_delivery = $this->pickupVsDelivery($date_range_start, $date_range_end);

        return ['labels' => $month_data['labels'], 'chart_values' => $month_data['data'],
            'user_sales' => $user_sales, 'item_sales' => $item_sales, 'device_sales' => $device_sales, 'pickup_delivery' => $pickup_delivery, 'day_of_week' => $day_of_week, 'summary' => $result];
    }

    public function monthData($first_of_month)
    {
        if ($this->filterType == 'single_merchant') {
            $response = DB::connection('reports_db')->select("select WEEK(order_dt_tm) as order_week, sum(order_amt) as weekly_sales, count(order_id) as order_count from Orders 
                  where order_dt_tm >= :date_range_start and merchant_id = :merchant_id and Orders.status = 'E' group by order_week order by order_week;",
                ["date_range_start" => $first_of_month, "merchant_id" => $this->data['merchant']]);
        } elseif ($this->filterType == 'brand') {
            $response = DB::connection('reports_db')->select("select WEEK(order_dt_tm) as order_week, sum(order_amt) as weekly_sales, count(order_id) as order_count from Orders 
                  join Merchant on Orders.merchant_id = Merchant.merchant_id 
                  where order_dt_tm >= :date_range_start and Merchant.brand_id = :brand_id and Orders.status = 'E' group by order_week order by order_week;",
                ["date_range_start" => $first_of_month, "brand_id" => $this->data['brand_id']]);
        } elseif ($this->filterType == 'multiple_merchants') {
            $response = DB::connection('reports_db')->select("select WEEK(order_dt_tm) as order_week, sum(order_amt) as weekly_sales, count(order_id) as order_count from Orders 
                  where order_dt_tm >= :date_range_start and merchant_id in  ( :merchants ) and Orders.status = 'E' group by order_week order by order_week;",
                ["date_range_start" => $first_of_month, "merchants" => $this->data['merchants']]);
        }

        $labels = [];
        $data = [];

        foreach ($response as $week) {
            if ($week->order_week < 10) {
                $first_day_of_week = date("m/d", strtotime(date("Y") . "W0" . $week->order_week . "1")); // First day of week
                $last_day_of_week = date("m/d", strtotime(date("Y") . "W0" . $week->order_week . "7")); // Last day of week
            } else {
                $first_day_of_week = date("m/d", strtotime(date("Y") . "W" . $week->order_week . "1")); // First day of week
                $last_day_of_week = date("m/d", strtotime(date("Y") . "W" . $week->order_week . "7")); // Last day of week
            }

            $labels[] = $first_day_of_week . "-" . $last_day_of_week;

            $data[0][] = $week->weekly_sales;
            $data[1][] = $week->order_count;
        }
        return ['labels' => $labels, 'data' => $data];
    }

    public function currentYearData(Request $request, Orders $model)
    {
        $date_range_start = date('Y') . "-01-01 00:00:00";
        $date_range_end = date('Y-m-d') . " 00:00:00";

        $this->data = $request->all();

        $result = $model->getOrdersSummary($this->data, $date_range_start);
        $this->setFilterType();

        $year_data = $this->yearData($date_range_start);

        $day_of_week = $this->getSalesByWeekday($date_range_start, $date_range_end);

        $user_sales = $this->userSalesLeaders($date_range_start, $date_range_end);
        $item_sales = $this->itemSalesLeaders($date_range_start, $date_range_end);
        $device_sales = $this->deviceTypeSales($date_range_start, $date_range_end);
        $pickup_delivery = $this->pickupVsDelivery($date_range_start, $date_range_end);

        return ['labels' => $year_data['labels'], 'chart_values' => $year_data['data'],
            'user_sales' => $user_sales, 'item_sales' => $item_sales, 'device_sales' => $device_sales, 'pickup_delivery' => $pickup_delivery, 'day_of_week' => $day_of_week, 'summary' => $result];
    }

    public function yearData($from_date)
    {

        if ($this->filterType == 'single_merchant') {
            $response = DB::connection('reports_db')->select("select MONTH(order_dt_tm) as order_month, sum(order_amt) as monthly_sales, count(order_id) as order_count from Orders 
                  where order_dt_tm >= :date_range_start and merchant_id = :merchant_id and Orders.status = 'E' group by order_month, merchant_id order by order_month;",
                ["date_range_start" => $from_date, "merchant_id" => $this->data['merchant']]);
        } elseif ($this->filterType == 'brand') {
            $response = DB::connection('reports_db')->select("select MONTH(order_dt_tm) as order_month, sum(order_amt) as monthly_sales, count(order_id) as order_count from Orders 
                  join Merchant on Orders.merchant_id = Merchant.merchant_id 
                  where order_dt_tm >= :date_range_start and Merchant.brand_id = :brand_id and Orders.status = 'E' group by order_month order by order_month;",
                ["date_range_start" => $from_date, "brand_id" => $this->data['brand_id']]);
        } elseif ($this->filterType == 'multiple_merchants') {
            $response = DB::connection('reports_db')->select("select MONTH(order_dt_tm) as order_month, sum(order_amt) as monthly_sales, count(order_id) as order_count from Orders 
                  where order_dt_tm >= :date_range_start and merchant_id in  ( :merchants ) and Orders.status = 'E' group by order_month order by order_month;",
                ["date_range_start" => $from_date, "merchants" => $this->data['merchants']]);
        }

        $labels = [];
        $data = [];

        foreach ($response as $month) {
            $labels[] = date("M", strtotime(date("Y") . "-" . $month->order_month . "-1"));

            $data[0][] = $month->monthly_sales;
            $data[1][] = $month->order_count;
        }
        return ['labels' => $labels, 'data' => $data];
    }

    public function userSalesLeaders($start, $end)
    {

        if ($this->filterType == 'single_merchant') {
            $response = DB::connection('reports_db')->select("select User.user_id, User.first_name, User.last_name, sum(Orders.order_amt) as user_sales from Orders join
                    User on Orders.user_id = User.user_id
                    where (Orders.order_dt_tm >= :date_range_start and Orders.order_dt_tm <= :date_range_end)
                    and Orders.merchant_id = :merchant_id and Orders.status = 'E' 
                    group by User.user_id, User.first_name, User.last_name order by user_sales desc limit 0, 5;",

                ["date_range_start" => $start, "date_range_end" => $end, "merchant_id" => $this->data['merchant']]);
        } elseif ($this->filterType == 'brand') {
            $response = DB::connection('reports_db')->select("select User.user_id, User.first_name, User.last_name, sum(Orders.order_amt) as user_sales from Orders join
                    User on Orders.user_id = User.user_id
                    join Merchant on Orders.merchant_id = Merchant.merchant_id 
                    where (Orders.order_dt_tm >= :date_range_start and Orders.order_dt_tm <= :date_range_end)
                    and Merchant.brand_id = :brand_id  and Orders.status = 'E' 
                    group by User.user_id, User.first_name, User.last_name order by user_sales desc limit 0, 5;",

                ["date_range_start" => $start, "date_range_end" => $end, "brand_id" => $this->data['brand_id']]);
        } elseif ($this->filterType == 'multiple_merchants') {
            $response = DB::connection('reports_db')->select("select User.user_id, User.first_name, User.last_name, sum(Orders.order_amt) as user_sales from Orders join
                    User on Orders.user_id = User.user_id
                    where (Orders.order_dt_tm >= :date_range_start and Orders.order_dt_tm <= :date_range_end)
                    and Orders.merchant_id in  ( :merchants ) and Orders.status = 'E' 
                    group by User.user_id, User.first_name, User.last_name order by user_sales desc limit 0, 5;",

                ["date_range_start" => $start, "date_range_end" => $end, "merchants" => $this->data['merchants']]);
        }
        return $response;
    }

    public function itemSalesLeaders($start, $end)
    {

        if ($this->filterType == 'single_merchant') {
            $response = DB::connection('reports_db')->select("select Item.item_id, Item.item_name, sum(Orders.order_amt) as item_sales from Orders join
                        Order_Detail on Orders.order_id = Order_Detail.order_id
                        join Item_Size_Map on Order_Detail.item_size_id = Item_Size_Map.item_size_id
                        join Item on Item_Size_Map.item_id = Item.item_id
                        where (Orders.order_dt_tm >= :date_range_start and Orders.order_dt_tm <= :date_range_end) 
                        and Orders.merchant_id = :merchant_id and Orders.status = 'E' 
                        group by Item.item_id, Item.item_name order by item_sales desc  limit 0, 5;",

                ["date_range_start" => $start, "date_range_end" => $end, "merchant_id" => $this->data['merchant']]);
        } elseif ($this->filterType == 'brand') {
            $response = DB::connection('reports_db')->select("select Item.item_id, Item.item_name, sum(Orders.order_amt) as item_sales from Orders join
                        Order_Detail on Orders.order_id = Order_Detail.order_id
                        join Item_Size_Map on Order_Detail.item_size_id = Item_Size_Map.item_size_id
                        join Item on Item_Size_Map.item_id = Item.item_id
                        join Merchant on Orders.merchant_id = Merchant.merchant_id 
                        where (Orders.order_dt_tm >= :date_range_start and Orders.order_dt_tm <= :date_range_end) 
                        and Merchant.brand_id = :brand_id and Orders.status = 'E' 
                        group by Item.item_id, Item.item_name order by item_sales desc  limit 0, 5;",

                ["date_range_start" => $start, "date_range_end" => $end, "brand_id" => $this->data['brand_id']]);
        } elseif ($this->filterType == 'multiple_merchants') {
            $response = DB::connection('reports_db')->select("select Item.item_id, Item.item_name, sum(Orders.order_amt) as item_sales from Orders join
                        Order_Detail on Orders.order_id = Order_Detail.order_id
                        join Item_Size_Map on Order_Detail.item_size_id = Item_Size_Map.item_size_id
                        join Item on Item_Size_Map.item_id = Item.item_id
                        where (Orders.order_dt_tm >= :date_range_start and Orders.order_dt_tm <= :date_range_end) 
                        and Orders.merchant_id in  ( :merchants ) and Orders.status = 'E' 
                        group by Item.item_id, Item.item_name order by item_sales desc  limit 0, 5;",

                ["date_range_start" => $start, "date_range_end" => $end, "merchants" => $this->data['merchants']]);
        }
        return $response;
    }

    public function deviceTypeSales($start, $end)
    {
        if ($this->filterType == 'single_merchant') {
            $response = DB::connection('reports_db')->select("select  device_type, sum(order_amt) as sales from Orders
                    where (order_dt_tm >= :date_range_start and order_dt_tm <= :date_range_end) and merchant_id = :merchant_id and Orders.status = 'E'  group by device_type",

                ["date_range_start" => $start, "date_range_end" => $end, "merchant_id" => $this->data['merchant']]);
        } elseif ($this->filterType == 'brand') {
            $response = DB::connection('reports_db')->select("select  device_type, sum(order_amt) as sales from Orders
                    join Merchant on Orders.merchant_id = Merchant.merchant_id 
                    where (order_dt_tm >= :date_range_start and order_dt_tm <= :date_range_end) and Merchant.brand_id = :brand_id  and Orders.status = 'E' group by device_type",

                ["date_range_start" => $start, "date_range_end" => $end, "brand_id" => $this->data['brand_id']]);
        } elseif ($this->filterType == 'multiple_merchants') {
            $response = DB::connection('reports_db')->select("select  device_type, sum(order_amt) as sales from Orders
                    where (order_dt_tm >= :date_range_start and order_dt_tm <= :date_range_end) and Orders.status = 'E' and Orders.merchant_id in  ( :merchants ) group by device_type",

                ["date_range_start" => $start, "date_range_end" => $end, "merchants" => $this->data['merchants']]);
        }


        $labels = [];
        $sales = [];

        foreach ($response as $device) {
            $labels[] = $device->device_type;
            $sales[] = $device->sales;
        }

        return ['labels' => $labels, 'sales' => $sales];
    }

    public function getNewVsReturningCustomers($start, $end)
    {

        if ($this->filterType == 'single_merchant') {
            $response = DB::connection('reports_db')->select("select Orders.user_id, user_order_counts.order_count
                    from Orders join 
                    (select user_id, count(order_id) as order_count from Orders
                    where merchant_id = :merchant_id group by user_id) user_order_counts
                    on Orders.user_id = user_order_counts.user_id 
                    where Orders.merchant_id = :merchant_id_outer and Orders.status = 'E' and (Orders.order_dt_tm >= :date_range_start and Orders.order_dt_tm <= :date_range_end)
                    group by Orders.user_id, user_order_counts.order_count",

                ["date_range_start" => $start, "date_range_end" => $end, "merchant_id" => $this->data['merchant'], "merchant_id_outer" => $this->data['merchant']]);
        } elseif ($this->filterType == 'brand') {
            $response = DB::connection('reports_db')->select("select Orders.user_id, user_order_counts.order_count
                    from Orders join 
                    (select user_id, count(order_id) as order_count from Orders group by user_id) user_order_counts
                    on Orders.user_id = user_order_counts.user_id 
                    join Merchant on Orders.merchant_id = Merchant.merchant_id 
                    where Merchant.brand_id = :brand_id_outer and Orders.status = 'E'  and (Orders.order_dt_tm >= :date_range_start and Orders.order_dt_tm <= :date_range_end)
                    group by Orders.user_id, user_order_counts.order_count",

                ["date_range_start" => $start, "date_range_end" => $end, "brand_id_outer" => $this->data['brand_id']]);
        } elseif ($this->filterType == 'multiple_merchants') {
            $response = DB::connection('reports_db')->select("select Orders.user_id, user_order_counts.order_count
                    from Orders join 
                    (select user_id, count(order_id) as order_count from Orders
                    where merchant_id in  ( :merchants ) group by user_id) user_order_counts
                    on Orders.user_id = user_order_counts.user_id 
                    where Orders.merchant_id in  ( :merchants_outer ) and Orders.status = 'E'  and (Orders.order_dt_tm >= :date_range_start and Orders.order_dt_tm <= :date_range_end)
                    group by Orders.user_id, user_order_counts.order_count",

                ["date_range_start" => $start, "date_range_end" => $end, "merchants" => $this->data['merchants'], "merchants_outer" => $this->data['merchants']]);
        }

        $chart_values['returning'] = 0;
        $chart_values['new'] = 0;

        foreach ($response as $user) {
            if ($user->order_count > 1) {
                $chart_values['returning']++;
            } else {
                $chart_values['new']++;
            }
        }
        $labels = ['Returning', 'New Customer'];

        return ['labels' => $labels, 'chart_values' => [$chart_values['returning'], $chart_values['new']]];
    }

    public function pickupVsDelivery($start, $end)
    {
        $response = [];

        if ($this->filterType == 'single_merchant') {
            $response = Orders::on('reports_db')
                ->whereBetween('order_dt_tm', [$start, $end])
                ->where('merchant_id', '=', $this->data['merchant'])
                ->where('status', '=', 'E')
                ->groupBy('order_type')->selectRaw('order_type, count(*) as order_type_count')->get();
        } elseif ($this->filterType == 'brand') {
            $response = Orders::on('reports_db')
                ->join('Merchant', 'Merchant.merchant_id', '=', 'Orders.merchant_id')
                ->whereBetween('order_dt_tm', [$start, $end])
                ->where('Merchant.brand_id', '=', $this->data['brand_id'])
                ->where('status', '=', 'E')
                ->groupBy('order_type')->selectRaw('order_type, count(*) as order_type_count')->get();
        } elseif ($this->filterType == 'multiple_merchants') {
            if (is_string($this->data['merchants'])) {
                $this->data['merchants'] = explode(',', str_replace(' ', '', $this->data['merchants']));
            }
            $response = Orders::on('reports_db')
                ->whereBetween('order_dt_tm', [$start, $end])
                ->where('status', '=', 'E')
                ->whereIn('merchant_id', $this->data['merchants'])
                ->groupBy('order_type')->selectRaw('order_type, count(*) as order_type_count')->get();
        }

        $chart_values['pickup'] = 0;
        $chart_values['delivery'] = 0;
        foreach ($response as $value) {
            if (!empty($value->order_type) && $value->order_type == 'R') {
                $chart_values['pickup'] += $value->order_type_count;
            }
            if (!empty($value->order_type) && $value->order_type == 'D') {
                $chart_values['delivery'] += $value->order_type_count;
            }
        }

        $labels = ['Pickup', 'Delivery'];

        return ['labels' => $labels, 'chart_values' => [$chart_values['pickup'], $chart_values['delivery']]];
    }

    public function getSalesByWeekday($start, $end)
    {
        if ($this->filterType == 'single_merchant') {
            $response = DB::connection('reports_db')->select("select DAYNAME(Orders.order_dt_tm) AS day_name, DAYOFWEEK(Orders.order_dt_tm) as day_of_week, sum(Orders.order_amt) as daily_sales 
                    from Orders
                    where (Orders.order_dt_tm >= :date_range_start and Orders.order_dt_tm <= :date_range_end)
                    and Orders.merchant_id = :merchant_id and Orders.status = 'E' 
                    group by day_name, day_of_week order by day_of_week;",

                ["date_range_start" => $start, "date_range_end" => $end, "merchant_id" => $this->data['merchant']]);
        } elseif ($this->filterType == 'brand') {
            $response = DB::connection('reports_db')->select("select DAYNAME(Orders.order_dt_tm) AS day_name, DAYOFWEEK(Orders.order_dt_tm) as day_of_week, sum(Orders.order_amt) as daily_sales 
                    from Orders
                    join Merchant on Orders.merchant_id = Merchant.merchant_id  
                    where (Orders.order_dt_tm >= :date_range_start and Orders.order_dt_tm <= :date_range_end)
                    and Merchant.brand_id = :brand_id and Orders.status = 'E' 
                    group by day_name, day_of_week order by day_of_week;",

                ["date_range_start" => $start, "date_range_end" => $end, "brand_id" => $this->data['brand_id']]);
        } elseif ($this->filterType == 'multiple_merchants') {
            $response = DB::connection('reports_db')->select("select DAYNAME(Orders.order_dt_tm) AS day_name, DAYOFWEEK(Orders.order_dt_tm) as day_of_week, sum(Orders.order_amt) as daily_sales 
                    from Orders
                    where (Orders.order_dt_tm >= :date_range_start and Orders.order_dt_tm <= :date_range_end)
                    and Orders.merchant_id in  ( :merchants ) and Orders.status = 'E' 
                    group by day_name, day_of_week order by day_of_week;",

                ["date_range_start" => $start, "date_range_end" => $end, "merchants" => $this->data['merchants']]);
        }


        $labels = [];
        $sales = [];

        foreach ($response as $day_sales) {
            $labels[] = $day_sales->day_name;
            $sales[] = $day_sales->daily_sales;
        }

        return ['labels' => $labels, 'sales' => $sales];
    }

    public function overview(Request $request) {
        $overviewReports = new OverviewReports();
        $overviewReports->request = $request->all();

        $response = $overviewReports->getReportData();
        return $response;
    }

    function createSchedule(Request $request, ReportSchedules $model)
    {
        try {
            $data = [];
            $data['report_name'] = $request->name;
            $data['frequency'] = $request->frequency;

            $data['start_date'] = $request->start_date;
            $data['end_date'] = $request->end_date;

            $data['recipient'] = $request->recipient;
            $payload = [];

            if ($request->report_type == 'customers') {
                $payload['set_time_range'] = $request->period_type['key'];
            }
            else {
                $payload['date_range'] = $request->period_start_date. ',' .$request->period_end_date;
            }

            $payload['group_by'] = array_map(function($gb) {
                return $gb['key'];
            }, $request->group_bys);
            $payload['group_by'] = implode(',', $payload['group_by']);
            $payload['selected_order_by'] = $request->selected_order_by;
            $payload['report_type'] = $request->report_type;

            if (isset($request->brand_id) || session('user_visibility') == 'brand') {
                if (session('user_visibility') == 'brand') {
                    $payload['brand'] = session('brand_manager_brand');
                }
                else {
                    $payload['brand'] = $request->brand_id;
                }
            }
            elseif (isset($request->selected_merchants)) {
                $payload['merchants'] = $request->selected_merchants;
            }

            $data['payload'] = json_encode($payload, true);

            $result = $model->addScheduleReport($data);

            $new_object_ownership = new ObjectOwnership();

            $new_object_ownership->user_id = Auth::user()->id;
            $new_object_ownership->organization_id = session('user_organization_id');
            $new_object_ownership->object_type = 'schdl_rpt';
            $new_object_ownership->object_id = $result->id;

            $new_object_ownership->save();

            return response()->json($result, 200);
        } catch (\Exception $exception) {
            return response()->json(['errors' => $exception->getMessage()], 400);
        }
    }
}