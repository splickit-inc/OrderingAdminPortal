<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class OperationHour extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'Operating_Hours';
    protected $primaryKey = 'hour_id';

    protected $fillable = ['merchant_id', 'day_of_week'];

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}
