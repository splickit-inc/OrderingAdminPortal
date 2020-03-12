<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MenuType extends Model
{

    protected $table = 'Menu_Type';
    protected $primaryKey = 'menu_type_id';

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function upsells()
    {
        return $this
            ->belongsToMany(MenuItem::class, 'Menu_Type_Item_Upsell_Maps', 'menu_type_id', 'item_id')
            ->withTimestamps('created', 'modified');
    }

    public function getUpsellsByMenuTypeRelatedToMenu($menu_id)
    {
        return $this
            ->where('menu_id', '=', $menu_id)->with('upsells')
            ->has('upsells')
            ->groupBy('menu_type_name')
            //->get(['*', 'menu_type_name as menu_type']); //its good to know that we can set what ever name we want.
            ->get();
    }
}
