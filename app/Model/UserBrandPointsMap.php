<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class UserBrandPointsMap extends Model implements Auditable  {
    use \OwenIt\Auditing\Auditable;
    protected $table = 'User_Brand_Points_Map';
    protected $connection = 'reports_db';

    public function brand() {
        return $this->belongsTo('App\Model\Brand', 'brand_id');
    }
}
