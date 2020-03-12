<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PortalMerchantGroupMerchantMap extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'portal_merchant_group_merchant_map';
    public $timestamps = false;
}
