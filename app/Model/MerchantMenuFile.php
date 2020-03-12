<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\LogicalDelete;
use OwenIt\Auditing\Contracts\Auditable;

class MerchantMenuFile extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'portal_merchant_menu_files';
    protected $fillable = ['merchant_id', 'url'];

    protected static function boot() {
        parent::boot();
        static::addGlobalScope(new LogicalDelete);
    }

}
