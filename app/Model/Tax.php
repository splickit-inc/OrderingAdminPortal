<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Tax extends Model implements Auditable {

    use \OwenIt\Auditing\Auditable;

    protected $table = 'Tax';
    protected $primaryKey = 'tax_id';

    protected $fillable = ['merchant_id', 'tax_group', 'locale', 'locale_description','rate'];

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    /**
     * Creates a tax object from customer data (provided in merchant form 2)
     * @param $merchantId
     * @param $taxRate
     * @return Tax
     */
    public static function fromCustomerData($merchantId, $taxRate){
        $tax = new Tax();
        $tax->fill(['merchant_id' => $merchantId,
            'locale' => 'Local', 'locale_description' => 'Default',
            'rate' => $taxRate]);
        return $tax;
    }

}