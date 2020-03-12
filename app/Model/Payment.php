<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Payment extends Model implements Auditable {

    use \OwenIt\Auditing\Auditable;

    protected $table = 'Merchant_Payment_Type_Maps';

    protected $fillable = ['merchant_id', 'splickit_accepted_payment_type_id', 'billing_entity_id', 'logical_delete'];

    public function billingEntity() {
        return $this->belongsTo('App\Model\BillingEntities', 'billing_entity_id');
    }

    public function paymentType() {
        return $this->belongsTo('App\Model\PaymentTypes', 'splickit_accepted_payment_type_id');
    }

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}
