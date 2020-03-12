<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PaymentTypes extends Model {

    protected $table = 'Splickit_Accepted_Payment_Types';

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}
