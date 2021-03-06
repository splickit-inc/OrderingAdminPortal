<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PortalBrandManagerBrandMap extends Model  implements Auditable  {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'portal_brand_manager_brand_map';
}
