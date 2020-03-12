<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\LogicalDelete;
use OwenIt\Auditing\Contracts\Auditable;

class PromoKeyWordMap extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'Promo_Key_Word_Map';
    protected $primaryKey = 'map_id';

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new LogicalDelete);
    }
}
