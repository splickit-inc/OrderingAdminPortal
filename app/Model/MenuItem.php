<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{

    protected $table = 'Item';
    protected $primaryKey = 'item_id';

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function menuTypesUpsellRelated()
    {
        return $this
            ->belongsToMany(MenuType::class, 'Menu_Type_Item_Upsell_Maps', 'item_id', 'menu_type_id')
            ->withTimestamps('created', 'modified');
    }

    public function menuUpsellRelated()
    {
        return $this
            ->belongsToMany(Menu::class, 'Menu_Upsell_Item_Maps', 'item_id', 'menu_id')
            ->withPivot(['active', 'logical_delete'])
            ->withTimestamps('created', 'modified');
    }
}
