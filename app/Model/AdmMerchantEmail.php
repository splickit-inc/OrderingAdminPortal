<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AdmMerchantEmail extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'adm_merchant_email';
    protected $primaryKey = 'id';

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}
