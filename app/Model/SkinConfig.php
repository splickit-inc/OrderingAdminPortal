<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;

class SkinConfig extends Model implements Auditable  {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'Skin_Config';
    protected $primaryKey = 'id';

    protected $fillable = ['brand_id', 'skin_id'];

    public function getBrandSkinsGlobal($brand_id) {
        $web_skins = DB::table('Skin_Config')
            ->where('Skin_Config.brand_id', '=', $brand_id)
            ->get()
            ->toArray();
        return $web_skins;
    }
}