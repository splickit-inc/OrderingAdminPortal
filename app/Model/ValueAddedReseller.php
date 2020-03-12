<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ValueAddedReseller extends Model implements Auditable  {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'portal_value_added_resellers';
    protected $primaryKey = 'id';
}