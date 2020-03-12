<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SplickitAcceptedPaymentTypes extends Model implements Auditable {

    use \OwenIt\Auditing\Auditable;

    protected $table = 'Splickit_Accepted_Payment_Types';
    protected $primaryKey = 'id';

    protected $fillable = ['merchant_id', 'tax_group', 'locale', 'locale_description','rate'];

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}