<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class HolidayHour extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'Holiday_Hour';
    protected $primaryKey = 'holiday_id';
    protected $fillable = ['the_date','day_open','open','close','second_close','logical_delete'];
    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

}
