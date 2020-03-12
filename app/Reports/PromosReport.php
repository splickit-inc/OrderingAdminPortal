<?php namespace App\Reports;

use Illuminate\Support\Facades\DB;

class PromosReport extends BaseReport {

    CONST reportFields = ['order_cnt', 'order_amt', 'promo_amt', 'delivery_amt', 'grand_total'];
    CONST headers = [];

    public $url_value = 'customers';

    public function setBaseQuery() {
        $this->query = DB::connection(PromosReport::CONNECTION)->table("rpt_aggregate_promo")
            ->join('Merchant', 'rpt_aggregate_promo.merchant_id', '=', 'Merchant.merchant_id');
    }

    public function setSelect() {
        if (isset($this->request['group_by'])) {
            if ($this->request['group_by']) {
                $this->all_group_bys = explode(',',$this->request['group_by']);
            }
            else {
                $this->all_group_bys = ['rpt_aggregate_promo.promo_id'];
            }

            foreach ($this->all_group_bys as $index=>$group_by) {
                if ($group_by == 'merchant_id') {
                    unset($this->all_group_bys[$index]);
                    $this->all_group_bys[] = 'Merchant.merchant_id';
                    $this->all_group_bys[] = 'Merchant.display_name as store_name';
                }
            }
        }
        else {
            $this->all_group_bys = ['rpt_aggregate_promo.promo_id'];
        }

        $this->group_by_select = $this->all_group_bys;


        $this->query = $this->query->select(
            DB::raw('SUM(rpt_aggregate_promo.order_cnt) as order_cnt'),
            DB::raw('SUM(rpt_aggregate_promo.order_amt) as order_amt'),
            DB::raw('SUM(rpt_aggregate_promo.promo_amt) as promo_amt'),
            DB::raw('SUM(rpt_aggregate_promo.delivery_amt) as delivery_amt'),
            DB::raw('SUM(rpt_aggregate_promo.grand_total) as grand_total'),
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
                    $this->query = $this->query->where('rpt_aggregate_promo.brand_id', '=', session('brand_manager_brand'));
                }
            } else {
                $this->query = $this->query->where('rpt_aggregate_promo.brand_id', '=', $this->request['brand']);
            }
        }

        $date_parameters = explode(',',$this->request['date_range']);
        $start_date = $date_parameters[0];
        $end_date = $date_parameters[1];

        $this->query = $this->query->where('rpt_aggregate_promo.Date', '>=', $start_date);
        $this->query = $this->query->where('rpt_aggregate_promo.Date', '<=', $end_date);
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

        if (sizeof($this->all_group_bys) > 0) {
            $this->query = $this->query->groupBy(DB::raw(implode(',', $this->all_group_bys)));
        }

    }
}