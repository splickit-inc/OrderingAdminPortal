<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class BusinessInfo extends Model
{
    protected $table = 'adm_w9';
    protected $fillable = ['filing_name', 'dba_name', 'address', 'city', 'state', 'zip', 'EIN_SS', 'merchant_id'];

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'merchant_id');
    }
}