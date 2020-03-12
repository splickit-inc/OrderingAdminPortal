<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BusinessBanking extends Model
{
    protected $table = 'adm_ach';
    protected $fillable = ['name_on_acct', 'routing', 'account', 'acct_address', 'acct_city', 'acct_st', 'acct_zip', 'merchant_id'];

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'merchant_id');
    }
}
