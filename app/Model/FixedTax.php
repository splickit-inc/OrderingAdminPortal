<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class FixedTax extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'Fixed_Tax';
    protected $fillable = ['merchant_id'];

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}
