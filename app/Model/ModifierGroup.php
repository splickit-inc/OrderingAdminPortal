<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ModifierGroup extends Model  implements Auditable {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'Modifier_Group';
    protected $primaryKey = 'modifier_group_id';

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}
