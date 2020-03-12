<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MenuTypeItemUpsellMaps extends Model
{
    protected $table = 'Menu_Type_Item_Upsell_Maps';

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}
