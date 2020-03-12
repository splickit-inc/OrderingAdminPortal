<?php namespace App\Reports;

use Illuminate\Support\Facades\DB;

class CustomerReport extends BaseReport {

    CONST reportFields = ['first_name', 'last_name', 'email',
        'total_orders', 'pickup_orders', 'delivery_orders',
        'total_spent', 'avg_order_value'];
    CONST headers = ['User ID', 'First Name', 'Last Name', 'Email',
        'Total Orders', 'Pickup Orders', 'Delivery Orders',
        'Total Spent', 'Average Order Value'];

    public $url_value = 'customers';

    public function setBaseQuery() {
        $this->query = DB::connection(CustomerReport::CONNECTION)->table("rpt_aggregate_users")
            ->join('User', 'rpt_aggregate_users.user_id', '=', 'User.user_id');
    }

    public function setSelect() {
        if (isset($this->request['group_by'])) {
            $this->all_group_bys = explode(',',$this->request['group_by']);
        }
        else {
            $this->all_group_bys = ['rpt_aggregate_users.user_id'];
        }

        foreach ($this->all_group_bys as $group_by) {
            if ($group_by == 'order_hour') {
                $this->group_by_select[] = 'CAST(order_hour AS SIGNED) as order_hour';
            }
            else {
                $this->group_by_select[] = 'rpt_aggregate_users.user_id';
            }
        }


        $this->query = $this->query->select('User.first_name','User.last_name','User.email', 'rpt_aggregate_users.user_id',
            DB::raw('MIN(rpt_aggregate_users.last_order) as first_order_date'),
            DB::raw('MAX(rpt_aggregate_users.last_order) as last_order_date'),
            DB::raw('SUM(rpt_aggregate_users.orders) as total_orders'),
            DB::raw('SUM(rpt_aggregate_users.orders) as total_orders'),
            DB::raw("SUM(case when order_type = 'R' then rpt_aggregate_users.orders else 0 end) as pickup_orders"),
            DB::raw("SUM(case when order_type = 'D' then rpt_aggregate_users.orders else 0 end) as delivery_orders"),
            DB::raw('SUM(rpt_aggregate_users.sales) as total_spent'),
            DB::raw('SUM(rpt_aggregate_users.sales)/SUM(rpt_aggregate_users.orders) as avg_order_value'),
            DB::raw(implode(',', $this->group_by_select)));
    }

    public function setWhereClause() {
        if (isset($this->request['merchants'])) {
            $merchant_list = implode(json_decode($this->request['merchants']), ', ');


            $this->query = $this->query->whereRaw('merchant_id in ('.$merchant_list.')');
        }
        elseif (isset($this->request['brand']) || session('user_visibility') == 'brand') {
            if (session('user_visibility') == 'brand') {
                if (isset($this->request['merchants'])) {
                    $merchant_list = implode(json_decode($this->request['merchants']), ', ');
                    $this->query = $this->query->whereRaw('merchant_id in ('.$merchant_list.')');
                }
                else {
                    $this->query = $this->query->where('brand_id', '=', session('brand_manager_brand'));
                }
            } else {
                $this->query = $this->query->where('brand_id', '=', $this->request['brand']);
            }
        }

        $this->query = $this->query->where('rpt_aggregate_users.period', '=', $this->request['set_time_range']);
    }

    public function setGroupBy() {
        $this->all_group_bys = [];

        if (isset($this->request['group_by'])) {
            if ($this->request['group_by']) {
                $this->all_group_bys = explode(',',$this->request['group_by']);
            }
        }
        else {
            $this->all_group_bys = [];
        }

        array_push($this->all_group_bys, 'rpt_aggregate_users.user_id');

        if (sizeof($this->all_group_bys) > 0) {
            $this->query = $this->query->groupBy(DB::raw(implode(',', $this->all_group_bys)));
        }

    }
}