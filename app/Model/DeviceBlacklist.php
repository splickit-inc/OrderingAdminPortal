<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class DeviceBlacklist extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'Device_Blacklist';
    protected $fillable = ['device_id'];

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}
