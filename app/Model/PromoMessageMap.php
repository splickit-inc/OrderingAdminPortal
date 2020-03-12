<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PromoMessageMap extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'Promo_Message_Map';
    protected $primaryKey = 'map_id';

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created = $model->freshTimestamp();
        });
    }
}
