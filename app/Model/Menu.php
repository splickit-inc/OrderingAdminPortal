<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;

class Menu extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'Menu';
    protected $primaryKey = 'menu_id';

    public $search_query;
    public $variable_bindings = [];

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function cartUpsells()
    {
        return $this
            ->belongsToMany(MenuItem::class, 'Menu_Upsell_Item_Maps', 'menu_id', 'item_id')
            ->withPivot(['active', 'logical_delete'])
            ->withTimestamps('created', 'modified');
    }

    /**
     * @param $menu_id
     * @return mixed
     * @throws \Exception
     */
    public function getCartUpsellsByMenu($menu_id)
    {
        try {
            return $this
                ->where('menu_id', '=', $menu_id)->with('cartUpsells')->first();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function searchGlobal($data)
    {
        $this->search_query = DB::table('Menu');

        $this->searchWhereClause($data['search_text']);

        $this->search_query->setBindings($this->variable_bindings);

        $this->search_query->orderBy($data['order_by']);

        return $this->search_query;
    }

    public function searchWhereClause($search_text)
    {
        $search_terms = explode(" ", $search_text);

        $this->variable_bindings = [];

        foreach ($search_terms as $search_term) {
            $this->search_query = $this->search_query->whereRaw("((Menu.menu_id = ? and Menu.menu_id != 0) or UPPER(Menu.name) like ? or UPPER(Menu.description) like ? or Menu.version = ?)");

            array_push($this->variable_bindings, $search_term, '%' . strtoupper($search_term) . "%",
                "%" . strtoupper($search_term) . "%", $search_term);
        }
        $this->search_query = $this->search_query->whereRaw("Menu.logical_delete = 'N'");
    }

    public function searchOperator($data)
    {

        $menus = DB::table('Menu')
            ->join('Merchant_Menu_Map', 'Menu.menu_id', '=', 'Merchant_Menu_Map.menu_id')
            ->join('portal_operator_merchant_map', 'Merchant_Menu_Map.merchant_id', '=', 'portal_operator_merchant_map.merchant_id')
            ->whereRaw('(portal_operator_merchant_map.user_id = ?) and name like ? and Menu.logical_delete = "N"')
            ->setBindings([Auth::user()->id, '%' . $data['search_text'] . "%"])
            ->distinct()
            ->get(['name', 'Menu.menu_id', 'external_menu_id', 'description', 'version'])
            ->toArray();
        return $menus;
    }

    public function searchMineOnly($data)
    {
        $this->search_query = DB::table('Menu')->join('portal_object_ownership', 'Menu.menu_id', '=', 'portal_object_ownership.object_id');

        $this->searchWhereClause($data['search_text']);

        $this->search_query->whereRaw('(portal_object_ownership.object_type = "menu" and portal_object_ownership.user_id in (?)) and Menu.logical_delete = "N"');

        $user_owner_ids = implode(', ', session('user_child_users'));

        array_push($this->variable_bindings, $user_owner_ids);

        $this->search_query->setBindings($this->variable_bindings);

        $this->search_query->distinct();

        $this->search_query->orderBy($data['order_by']);

        return $this->search_query;
    }

    public function searchOrganizationAll($data)
    {
        $this->search_query = DB::table('Menu')->join('portal_object_ownership', 'Menu.menu_id', '=', 'portal_object_ownership.object_id');

        $this->searchWhereClause($data['search_text']);

        $this->search_query = $this->search_query->whereRaw("(portal_object_ownership.object_type = 'menu' and portal_object_ownership.organization_id = ?) and (logical_delete = 'N')");

        array_push($this->variable_bindings, session('user_organization_id'));

        $this->search_query->setBindings($this->variable_bindings);

        $this->search_query->distinct();

        $this->search_query->orderBy($data['order_by']);

        return $this->search_query;
    }

    public function searchBrand($data)
    {
        $this->search_query = DB::table('Menu')
            ->join('portal_brand_manager_brand_map', 'Menu.brand_id', '=', 'portal_brand_manager_brand_map.brand_id');

        $this->searchWhereClause($data['search_text']);

        $this->search_query = $this->search_query->whereRaw("(portal_brand_manager_brand_map.user_id = ? and Menu.logical_delete = 'N')");

        array_push($this->variable_bindings, Auth::user()->id);

        $this->search_query->setBindings($this->variable_bindings);

        $this->search_query->distinct();

        $this->search_query->orderBy($data['order_by']);

        return $this->search_query;
    }

    public function getPromoBogoOptions($menu_id)
    {
        $menu_type_sizes = DB::select('select distinct Menu_Type.menu_type_id, Menu_Type.menu_type_name, 
                Sizes.size_id, Sizes.size_name
                 from Menu_Type join 
                Sizes on Menu_Type.menu_type_id = Sizes.menu_type_id
                where Menu_Type.menu_id = ? order by Menu_Type.priority, Sizes.priority', [$menu_id]);

        $items = DB::select('select distinct Menu_Type.menu_type_id, Menu_Type.menu_type_name, 
                Item.item_id, Item.item_name
                 from Menu_Type join 
                Item on Menu_Type.menu_type_id = Item.menu_type_id
                where Menu_Type.menu_id = ? order by Menu_Type.priority, Item.priority', [$menu_id]);

        $item_sizes = DB::select('select distinct Menu_Type.menu_type_id, Menu_Type.menu_type_name, 
                Item.item_id, Item.item_name, Item_Size_Map.item_size_id, Sizes.size_name 
                 from Menu_Type join 
                Item on Menu_Type.menu_type_id = Item.menu_type_id
                join Item_Size_Map on Item.item_id = Item_Size_Map.item_id
                join Sizes on Item_Size_Map.size_id = Sizes.size_id 
                where Menu_Type.menu_id = ? and Item_Size_Map.merchant_id = 0 order by Menu_Type.priority, Item.priority, Sizes.priority;', [$menu_id]);

        return ['menu_type_sizes' => $menu_type_sizes, 'items' => $items, 'item_sizes' => $item_sizes];
    }

    /**
     * @param $brand_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getMenusByBrandId($brand_id)
    {
        $menus = $this->newQuery()
            ->where('brand_id', '=', $brand_id);
        return $menus;
    }

    public function pageLoadBrand()
        {
        $menus = DB::connection('reports_db')->table('Menu')
            ->join('portal_brand_manager_brand_map', 'Menu.brand_id', '=', 'portal_brand_manager_brand_map.brand_id')
            ->whereRaw('(portal_brand_manager_brand_map.user_id = ?) and Menu.logical_delete = "N"')
            ->setBindings([Auth::user()->id])
            ->take(100)
            ->distinct()
            ->get(['Menu.name', 'Menu.menu_id', 'external_menu_id', 'Menu.description', 'Menu.version']);
        return $menus;
    }

    public function pageLoadAll()
    {
        $menus = DB::connection('reports_db')->table('Menu')
            ->join('portal_object_ownership', 'Menu.menu_id', '=', 'portal_object_ownership.object_id')
            ->whereRaw('(portal_object_ownership.object_type = "menu" and portal_object_ownership.organization_id = ?)and Menu.logical_delete = "N"')
            ->setBindings([session('user_organization_id')])
            ->take(100)
            ->distinct()
            ->get(['name', 'menu_id', 'external_menu_id', 'description', 'version']);
        return $menus;
    }

    public function pageLoadGlobal()
    {
        $menus = Menu::where('logical_delete', '=', 'N')
            ->take(100)
            ->get(['name', 'menu_id', 'external_menu_id', 'description', 'version'])
            ->sortByDesc('modified');
        return $menus;
    }

    public function pageLoadMineOnly()
    {
        $user_owner_ids = implode(', ', session('user_child_users'));

        $menus = DB::table('Menu')
            ->join('portal_object_ownership', 'Menu.menu_id', '=', 'portal_object_ownership.object_id')
            ->whereRaw('(portal_object_ownership.object_type = "menu" and portal_object_ownership.user_id in (?)) and Menu.logical_delete = "N"')
            ->setBindings([$user_owner_ids])
            ->distinct()
            ->get(['name', 'menu_id', 'external_menu_id', 'description', 'version']);
        return $menus;
    }
}
