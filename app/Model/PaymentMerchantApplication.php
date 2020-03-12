<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PaymentMerchantApplication extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'payment_merchant_applications';

    protected $fillable = [
        'merchant_id',
        'business_name',
        'phone',
        'email',
        'dba',
        'fed_tax_id',
        'bank_account_number',
        'bank_routing_number',
        'bank_type_saving_checking',
        'bank_type_business_personal',
        'primary_owner_first_name',
        'primary_owner_last_name',
        'primary_owner_dob',
        'primary_owner_ss',
        'primary_owner_phone',
        'primary_owner_email',
        'primary_owner_drivers_license_url',
        'primary_owner_void_check_url',
        'vio_result_status',
        'vio_account_number',
    ];

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'merchant_id');
    }
}