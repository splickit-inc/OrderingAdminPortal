<?php namespace App\Reports;

use Illuminate\Support\Facades\DB;

class StatementsReport extends BaseReport {

    public $url_value = 'transactions';

    public function setBaseQuery() {
        $this->query = DB::connection(StatementsReport::CONNECTION)->table("rpt_aggregate_orders")
            ->join('Merchant', 'rpt_aggregate_orders.merchant_id', '=', 'Merchant.merchant_id');
    }

    public function setSelect() {
        if ($this->request['group_by'] == 'order_month') {
            $this->group_by_select = 'MONTHNAME(order_date) order_month';
        }
        elseif ($this->request['group_by'] == 'order_date') {
            $this->group_by_select = 'DATE(order_date) order_date';
        }
        elseif ($this->request['group_by'] == 'order_hour') {
            $this->group_by_select = 'order_hour';
        }
        elseif ($this->request['group_by'] == 'order_day_of_week') {
            $this->group_by_select = 'DAYNAME(order_date) order_day_of_week';
        }
        elseif ($this->request['group_by'] == 'order_day_of_week') {
            $this->group_by_select = 'DAYNAME(order_date) order_day_of_week';
        }
        elseif ($this->request['group_by'] == 'order_year') {
            $this->group_by_select = 'YEAR(order_date) order_year';
        }
        elseif ($this->request['group_by'] == 'order_hour') {
            $this->group_by_select = 'order_hour';
        }


        $this->query = $this->query->select('rpt_aggregate_orders.*','Merchant.display_name as store_name',
            DB::raw('SUM(rpt_aggregate_orders.order_cnt) as total_orders'),
            DB::raw('SUM(rpt_aggregate_orders.is_pickuup_order) as pickup_orders'),
            DB::raw('SUM(rpt_aggregate_orders.is_delivery_order) as delivery_orders'),
            DB::raw('SUM(rpt_aggregate_orders.order_amt) as order_amount'),
            DB::raw('SUM(rpt_aggregate_orders.tip_amt) as tip'),
            DB::raw('SUM(rpt_aggregate_orders.total_tax_amt) as tax'),
            DB::raw('SUM(rpt_aggregate_orders.promo_amt) as discount'),
            DB::raw('SUM(rpt_aggregate_orders.delivery_amt) as delivery_fee'),
            DB::raw('SUM(rpt_aggregate_orders.grand_total) as grand_total'),
            DB::raw('AVG(rpt_aggregate_orders.order_amt) as avg_order_value'),
            DB::raw($this->group_by_select));
    }

    public function setWhereClause() {
        if (isset($this->request['merchants'])) {
            $merchant_list = implode(json_decode($this->request['merchants']), ', ');


            $this->query = $this->query->whereRaw('Merchant.merchant_id in ('.$merchant_list.')');
        }
        elseif (isset($this->request['brand']) || session('user_visibility') == 'brand') {
            if (session('user_visibility') == 'brand') {
                if (isset($this->request['merchants'])) {
                    $merchant_list = implode(json_decode($this->request['merchants']), ', ');
                    $this->query = $this->query->whereRaw('Merchant.merchant_id in ('.$merchant_list.')');
                }
                else {
                    $this->query = $this->query->where('Merchant.brand_id', '=', session('brand_manager_brand'));
                }
            } else {
                $this->query = $this->query->where('Merchant.brand_id', '=', $this->request['brand']);
            }
        }

        $date_parameters = explode(',',$this->request['date_range']);
        $start_date = $date_parameters[0];
        $end_date = $date_parameters[1];

        $this->query = $this->query->where('rpt_aggregate_orders.order_date', '>=', $start_date);
        $this->query = $this->query->where('rpt_aggregate_orders.order_date', '<=', $end_date);
    }

    public function setGroupBy() {
        $this->query = $this->query->groupBy($this->request['group_by'], 'rpt_aggregate_orders.merchant_id');
    }
}