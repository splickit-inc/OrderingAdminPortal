<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AdmMerchantPhone extends Model implements Auditable {

    use \OwenIt\Auditing\Auditable;

    protected $table = 'adm_merchant_phone';
    protected $primaryKey = 'id';

    protected $fillable = ['merchant_id', 'name', 'title', 'phone_no', 'email'];

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    /**
     * Creates a new AdmMerchantPhone filling the basic fields with the Lead information
     *
     * @param Lead $lead
     * @param      $merchantId
     * @return AdmMerchantPhone
     */
    public static function fromLead(Lead $lead, $merchantId) {
        $admMerchantPhone = new AdmMerchantPhone();
        $admMerchantPhone->fill(['merchant_id' => $merchantId, 'name' => $lead['contact_name'],
            'title' => $lead['contact_title'],
            'phone_no' => str_replace(' ', '', $lead['contact_phone_no']),
            'email' => $lead['contact_email']]);
        return $admMerchantPhone;
    }
}
