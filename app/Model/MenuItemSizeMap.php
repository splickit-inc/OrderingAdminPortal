<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MenuItemSizeMap extends Model
{

    protected $table = 'Item_Size_Map';
    protected $primaryKey = 'item_size_id';

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}
