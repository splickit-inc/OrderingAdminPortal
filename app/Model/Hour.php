<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Hour extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'Hour';
    protected $primaryKey = 'hour_id';

    protected $fillable = ['merchant_id', 'day_of_week','day_open','hour_type','second_close'];

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

}
