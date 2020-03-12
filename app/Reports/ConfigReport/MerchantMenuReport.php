<?php namespace App\Reports\ConfigReport;

use Illuminate\Support\Facades\DB;
use App\Reports\BaseReport;

class MerchantMenuReport extends BaseReport {

    public $request;
    public $url_value = 'merchant_config';

    public function setBaseQuery() {
        $this->query = DB::connection('reports_db')->table("Merchant")
            ->leftJoin('Merchant_Catering_Infos', 'Merchant.merchant_id', 'Merchant_Catering_Infos.merchant_id');
    }

    public function setWhereClause()
    {
        if (isset($this->request['merchants'])) {
            $merchant_list = implode(json_decode($this->request['merchants']), ', ');


            $this->query = $this->query->whereRaw('Merchant.merchant_id in (' . $merchant_list . ')');
        }
        elseif (isset($this->request['brand']) || session('user_visibility') == 'brand') {
            if (session('user_visibility') == 'brand') {
                $brand = session('brand_manager_brand');
            } else {
                $brand = $this->request['brand'];
            }
            $this->query = $this->query->where('Merchant.brand_id', '=', $brand);
        }
    }

    public function setGroupBy() {

    }

    public function setSelect() {
        $this->query = $this->query->select('Merchant.merchant_id',
            DB::raw("if(Merchant.delivery='Y','On','Off') as delivery"),
            DB::raw("if(Merchant.ordering_on='Y','On','Off') as ordering_on"),
            DB::raw("if(Merchant.advanced_ordering=1,'On','Off') as advanced_ordering"),
            DB::raw("if(Merchant.group_ordering_on='Y','On','Off') as group_ordering_on"),
            DB::raw("if(Merchant_Catering_Infos.active='Y','On','Off') as catering_on"));
    }
}