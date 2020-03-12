<?php namespace App\Model;

use App\BusinessModel\Orders\ReportFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use OwenIt\Auditing\Contracts\Auditable;

class Orders extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'Orders';
    protected $connection = 'reports_db';

    protected $primaryKey = 'order_id';

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';


    /**
     * @param $parameters
     * @param string $startDate
     * @param string $endDate
     * @return mixed
     */
    public function getOrdersSummary($parameters, $startDate = '', $endDate = '')
    {
        $this->setConnection('reports_db');
        $filterType = ReportFactory::getFilterType($parameters);
        $query = $this->newQuery();
        $query = $query->where($this->table . '.status', '=', 'E');

        if (!empty($startDate)) {
            $query = $query->where($this->table . '.order_dt_tm', '>=', $startDate);
        }

        if (!empty($endDate)) {
            $query = $query->where($this->table . '.order_dt_tm', '<=', $endDate);
        }

        if (!empty($filterType)) {
            $query = $filterType->addFilterTypeToQuery($query);
            return $query->selectRaw("ROUND( avg(order_amt), 2) as order_average, count(*) as order_count, sum(order_qty) as item_count, sum(order_amt) as order_total,  
            sum(total_tax_amt) as tax_total, sum(tip_amt) as total_tip, sum(promo_amt) as promo_total, sum(delivery_amt) as delivery_total, sum(grand_total) as total_grand_total")->get()->first();
        }
        return [
            "order_count" => 0,
            "item_count" => "0",
            "order_total" => "0.00",
            "tax_total" => "0.00",
            "total_tip" => "0.00",
            "promo_total" => "0.00",
            "delivery_total" => "0.00",
            "total_grand_total" => "0.0"
        ];
    }

    function getOrderCountAttribute($value)
    {
        return $this->checkIfEmpty($value);
    }

    function getItemCountAttribute($value)
    {
        return $this->checkIfEmpty($value);
    }

    function getOrderTotalAttribute($value)
    {
        return $this->checkIfEmpty($value);
    }

    function getTaxTotalAttribute($value)
    {
        return $this->checkIfEmpty($value);
    }

    function getTotalTipAttribute($value)
    {
        return $this->checkIfEmpty($value);
    }

    function getPromoTotalAttribute($value)
    {
        return $this->checkIfEmpty($value);
    }

    function getDeliveryTotalAttribute($value)
    {
        return $this->checkIfEmpty($value);
    }

    function getTotalGrandTotalAttribute($value)
    {
        return $this->checkIfEmpty($value);
    }

    function getOrderAverageAttribute($value)
    {
        return $this->checkIfEmpty($value);
    }

    function checkIfEmpty($value)
    {
        if (empty($value)) {
            return 0;
        }
        return $value;
    }
}
