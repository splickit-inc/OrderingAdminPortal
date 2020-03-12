<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Model\PortalMerchantGroupMerchantMap;
use App\Model\PortalMerchantGroupGroupMap;

class PortalMerchantGroups extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'portal_merchant_groups';


    public $potential_children_only = false;

    public function searchGlobal($search_text) {
        $merchant_groups = DB::table('portal_merchant_groups');

        if ($this->potential_children_only) {
            $merchant_groups = $merchant_groups
                ->whereRaw('group_type = "merchant" and group_parent_id is null');
        }

        $merchant_groups = $merchant_groups
            ->whereRaw('name like ? and logical_delete = "N"')
            ->setBindings([ "%" . $search_text . "%"]);

        return $merchant_groups->get()
            ->toArray();
    }

    public function searchBrand($search_text) {
        $merchant_groups = DB::table('portal_merchant_groups');

        if ($this->potential_children_only) {
            $merchant_groups = $merchant_groups
                ->whereRaw('group_type = "merchant" and group_parent_id is null');
        }

        $merchant_groups = $merchant_groups
            ->whereRaw('name like ? and logical_delete = "N" and brand_id = ?')
            ->setBindings(["%" . $search_text . "%", session('brand_manager_brand')]);

        return $merchant_groups->get(['portal_merchant_groups.*'])
            ->toArray();
    }

    public function loadGlobal() {
        $merchant_groups = DB::table('portal_merchant_groups');

        $merchant_groups = $merchant_groups
            ->where('logical_delete', '=', 'N');

        return $merchant_groups->get()
            ->toArray();
    }

    public function loadBrand() {
        $merchant_groups = DB::table('portal_merchant_groups')
                            ->where('portal_merchant_groups.brand_id', '=', session('brand_manager_brand'));

        $merchant_groups = $merchant_groups
            ->where('logical_delete', '=', 'N');

        return $merchant_groups->get(['portal_merchant_groups.*'])
            ->toArray();
    }

    public function getMerchantIds($merchant_group_id) {
        $merchant_group = $this::find($merchant_group_id);

        if ($merchant_group->group_type == 'merchant') {

            $merchants = DB::table('portal_merchant_group_merchant_map')
                ->join('Merchant_Menu_Map', 'portal_merchant_group_merchant_map.merchant_id', 'Merchant_Menu_Map.merchant_id')
                ->where('portal_merchant_group_merchant_map.group_id', '=', $merchant_group_id)
                ->distinct()
                ->get(['portal_merchant_group_merchant_map.merchant_id'])
                ->toArray();

            return implode(',',array_column($merchants, 'merchant_id'));
        }
        elseif ($merchant_group->group_type == 'group') {
            $merchant_groups = PortalMerchantGroupGroupMap::where('parent_group_id', '=', $merchant_group_id)->get()->toArray();

            $all_merchants = [];
            foreach ($merchant_groups as $merchant_group) {

                $merchants = DB::table('portal_merchant_group_merchant_map')
                    ->join('Merchant_Menu_Map', 'portal_merchant_group_merchant_map.merchant_id', 'Merchant_Menu_Map.merchant_id')
                    ->where('portal_merchant_group_merchant_map.group_id', '=', $merchant_group['child_group_id'])
                    ->distinct()
                    ->get(['portal_merchant_group_merchant_map.merchant_id'])
                    ->toArray();


                foreach ($merchants as $merchant) {
                    $all_merchants[] = $merchant->merchant_id;
                }
            }
            return implode(',',array_unique($all_merchants));
        }
    }

    public function merchants()
    {
        return $this->belongsToMany(Merchant::class,'portal_merchant_group_merchant_map', 'group_id', 'merchant_id');
    }
}
