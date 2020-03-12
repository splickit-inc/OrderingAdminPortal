<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PaymentServiceBusiness extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'payment_services_business';

    protected $fillable = [
        'dba',
        'fed_tax_id',
        'primary_owner_ssn',
        'bank_account_number',
        'bank_routing_number',
        'physical_address1',
        'physical_address2',
        'physical_city',
        'physical_state',
        'physical_zip',
        'physical_phone',
        'business_url',
        'ave_ticket_size',
        'max_ticket_size',
        'ave_annual_volume',
        'apply_for_amex',
    ];

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'merchant_id');
    }
}