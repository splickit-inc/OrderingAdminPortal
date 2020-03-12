<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PaymentServiceOwner extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'payment_services_owner';

    protected $fillable = [
        'primary_owner_first_name',
        'primary_owner_last_name',
        'primary_owner_dob',
        'primary_owner_address1',
        'primary_owner_address2',
        'primary_owner_city',
        'primary_owner_state',
        'primary_owner_zip',
        'primary_owner_phone',
        'legal_email',
        'logical_delete'
    ];

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'merchant_id');
    }
}