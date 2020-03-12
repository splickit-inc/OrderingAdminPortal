<?php namespace App\Model\AggregateReports;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class RptAggregateOrders extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'rpt_aggregate_orders';
    protected $primaryKey = 'id';

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}
