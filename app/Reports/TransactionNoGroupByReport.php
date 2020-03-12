<?php namespace App\Reports;

use Illuminate\Support\Facades\DB;

class TransactionNoGroupByReport extends BaseReport {

    public $url_value = 'transactions';

    public function setBaseQuery() {
        $this->query = DB::connection(TransactionReport::CONNECTION)->table("Orders")
            ->join('Merchant', 'Orders.merchant_id', '=', 'Merchant.merchant_id');
    }

    public function setSelect() {
        $this->query = $this->query->select('Orders.*',
            DB::raw('DATE_FORMAT(order_dt_tm, "%m/%d/%Y") as order_date'));
    }

    public function setWhereClause() {
        if (isset($this->request['merchants'])) {
            $merchant_list = implode(json_decode($this->request['merchants']), ', ');


            $this->query = $this->query->whereRaw('Merchant.merchant_id in ('.$merchant_list.')');
        }
        elseif (isset($this->request['brand']) || session('user_visibility') == 'brand') {
            if (session('user_visibility') == 'brand') {
                $brand = session('brand_manager_brand');
            }
            else {
                $brand = $this->request['brand'];
            }
            $this->query = $this->query->where('Merchant.brand_id', '=', $brand);
        }

        $date_parameters = explode(',',$this->request['date_range']);
        $start_date = $date_parameters[0];
        $end_date = $date_parameters[1];

        $this->query = $this->query->where('Orders.order_dt_tm', '>=', $start_date);
        $this->query = $this->query->where('Orders.order_dt_tm', '<=', $end_date);
    }

    public function setGroupBy() {

    }
}