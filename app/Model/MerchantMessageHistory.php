<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class MerchantMessageHistory extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;
    protected $table = 'Merchant_Message_History';
    protected $primaryKey = 'map_id';
    protected $connection = 'reports_db';

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    protected $fillable = ['order_id'];

    public function messageFormat() {
        return $this->hasOne('App\Model\Lookup', 'type_id_value', 'message_format')->where('type_id_field', '=', 'order_del_type');
    }
}
