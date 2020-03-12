<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;

class DefaultSkinConfig extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'Portal_Default_Skin_Config';
    protected $primaryKey = 'id';

    public function getDefaultSkins() {
        $template_skins = DB::table('Portal_Default_Skin_Config')
            ->get()
            ->toArray();

        return $template_skins;
    }
}