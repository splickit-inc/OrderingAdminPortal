<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class MerchantMessageMap extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;
    protected $table = 'Merchant_Message_Map';
    protected $primaryKey = 'map_id';


    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    protected $fillable = ['merchant_id'];

    /**
     * @param       $merchant_id
     * @return MerchantMessageMap
     */
    public function getCurrentMessageForVivonet($merchant_id) {
        return $this->where(
            ['merchant_id' => $merchant_id, 'message_format' => 'V']
        )->first();
    }

}
