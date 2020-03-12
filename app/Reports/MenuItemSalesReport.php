<?php namespace App\Reports;

use Illuminate\Support\Facades\DB;

class MenuItemSalesReport extends BaseReport {

    CONST reportFields = ['item_size_id', 'Size_Name', 'Item_Name', 'Menu_Type', 'Date', 'Item_Total', 'Item_Count'];
    CONST headers = ['Item Size Id', 'Size Name', 'Item Name', 'Menu Type', 'Date', 'Item Total', 'Item Count'];

    public $url_value = 'transactions';

    public function setBaseQuery() {
        $this->query = DB::connection(MenuItemSalesReport::CONNECTION)->table("rpt_aggregate_menu_type")
            ->join('Merchant', 'rpt_aggregate_menu_type.merchant_id', '=', 'Merchant.merchant_id');
    }

    public function setSelect() {
        $this->all_group_bys = explode(',',$this->request['group_by']);

        foreach ($this->all_group_bys as $group_by) {
            if ($group_by == 'order_month') {
                $this->group_by_select[] = 'MONTHNAME(Date) order_month';
                $this->group_by_select[] = 'CAST(DATE_FORMAT(Date,"%c") AS SIGNED) as order_month_d';
            }
            elseif ($group_by == 'order_date') {
                $this->group_by_select[] = 'DATE(Date) order_date';
            }
            elseif ($group_by == 'order_hour') {
                $this->group_by_select[] = 'order_hour';
            }
            elseif ($group_by == 'order_day_of_week') {
                $this->group_by_select[] = 'DAYNAME(Date) order_day_of_week';
            }
            elseif ($group_by == 'order_year') {
                $this->group_by_select[] = 'YEAR(Date) order_year';
            }
            elseif ($group_by == 'order_hour') {
                $this->group_by_select[] = 'order_hour';
            }
            elseif ($group_by == 'merchant_id') {
                $this->group_by_select[] = 'Merchant.merchant_id';
                $this->group_by_select[] = 'Merchant.display_name as store_name';
            }
        }

        if (sizeof($this->group_by_select) > 0) {
            $group_by_select = DB::raw(implode(',', $this->group_by_select));
            $this->query = $this->query->select('rpt_aggregate_menu_type.item_size_id',
                'rpt_aggregate_menu_type.Size_Name',
                'rpt_aggregate_menu_type.Item_Name',
                'rpt_aggregate_menu_type.Menu_Type', 'rpt_aggregate_menu_type.Date',
                DB::raw('SUM(rpt_aggregate_menu_type.Item_Total) as Item_Total'),
                DB::raw('SUM(rpt_aggregate_menu_type.Item_Count) as Item_Count'),
                DB::raw('CONCAT(rpt_aggregate_menu_type.Size_Name, rpt_aggregate_menu_type.Item_Name) as size_item_name'),
                DB::raw('SUM(rpt_aggregate_menu_type.Item_Total_with_Modifiers) as Item_Total_with_Modifiers'),
                $group_by_select);
        }
        else {
            $this->query = $this->query->select('rpt_aggregate_menu_type.item_size_id','rpt_aggregate_menu_type.Size_Name', 'rpt_aggregate_menu_type.Item_Name',
                'rpt_aggregate_menu_type.Menu_Type', 'rpt_aggregate_menu_type.Date',
                DB::raw('SUM(rpt_aggregate_menu_type.Item_Total) as Item_Total'),
                DB::raw('SUM(rpt_aggregate_menu_type.Item_Count) as Item_Count'),
                DB::raw('CONCAT(rpt_aggregate_menu_type.Size_Name, rpt_aggregate_menu_type.Item_Name) as size_item_name'),
                DB::raw('SUM(rpt_aggregate_menu_type.Item_Total_with_Modifiers) as Item_Total_with_Modifiers'));
        }
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
            }
            else {
                $this->query = $this->query->where('Merchant.brand_id', '=', $this->request['brand']);
            }

        }

        $date_parameters = explode(',',$this->request['date_range']);
        $start_date = $date_parameters[0];
        $end_date = $date_parameters[1];

        $this->query = $this->query->where('rpt_aggregate_menu_type.Date', '>=', $start_date);
        $this->query = $this->query->where('rpt_aggregate_menu_type.Date', '<=', $end_date);
    }

    public function setGroupBy() {
        $all_group_by_raw = [];

        if (isset($this->request['group_by'])) {
            if ($this->request['group_by']) {
                $all_group_bys = explode(',',$this->request['group_by']);

                foreach ($all_group_bys as $group_by) {
                    if ($group_by == 'order_month') {
                        $all_group_by_raw[] = 'order_month_d';
                    }
                    else {
                        $all_group_by_raw[] = $group_by;
                    }
                }
            }
            else {
                $this->all_group_bys = [];
            }
        }
        else {
            $this->all_group_bys = [];
        }
        $all_group_by_raw[] = 'size_item_name';
        $this->query = $this->query->groupBy(DB::raw(implode(',', $all_group_by_raw)));

    }
}