<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Property extends Model  implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'Property';

    const ONBOARDING_RECIPIENT_EMAIL = 'onboarding_recipient_email';

    /**
     * Gets the property matching the name key provided
     * @param string $name
     * @return Property|null null if no property with the matching name was found
     */
    public static function property($name){
        return self::where('name', $name)->first();
    }

    /**
     * Gets the value of the property matching the name key provided
     * @param string $propertyName
     * @return string|null null if no property with the matching name was found
     */
    public static function valueOf($propertyName){
        $property =  self::property($propertyName);
        if(is_null($property)){
            return null;
        }
        return $property['value'];
    }
}
