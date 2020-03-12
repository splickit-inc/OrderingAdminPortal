<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ModifierItem extends Model {

    protected $table = 'Modifier_Item';
    protected $primaryKey = 'modifier_item_id';

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}
