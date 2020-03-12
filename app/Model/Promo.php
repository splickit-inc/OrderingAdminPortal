<?php namespace App\Model;

use App\BusinessModel\Visibility\promos\IVisibility;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\LogicalDelete;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;
use App\Service\Utility;

class Promo extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'Promo';
    protected $primaryKey = 'promo_id';

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public $promo_types = [
        1 => 'Cart Discount',
        2 => 'BOGO',
        300 => 'Delivery Discount',
        4 => 'Item Discount',
        5 => 'Bundle Discount'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new LogicalDelete);
    }

    function getStartDateAttribute()
    {
        return $this->getDateWithFormat($this->attributes['start_date'], 'm/d/Y');
    }

    function getEndDateAttribute()
    {
        return $this->getDateWithFormat($this->attributes['end_date'], 'm/d/Y');
    }

    protected function getDateWithFormat($stringDate, $format)
    {
        return date($format, strtotime($stringDate));
    }


    /**
     * @param Builder $query
     * @return Builder
     */
    public function applyMerchantAndKeyMap($query)
    {
        return $query
            ->leftJoin('Promo_Merchant_Map', 'Promo.promo_id', '=', 'Promo_Merchant_Map.promo_id')
            ->leftJoin('Promo_Key_Word_Map', 'Promo.promo_id', '=', 'Promo_Key_Word_Map.promo_id')
            ->where('Promo.logical_delete', '=', 'N');
    }

    /**
     * @param Builder $query
     * @param String $filter
     * @param $active_only
     * @return Builder
     */
    public function applyFilter($query, $filter, $active_only, $promo_type)
    {
        $search_terms = explode(" ", $filter);

        foreach ($search_terms as $search_term) {
            $query = $query->whereRaw("(Promo.promo_id = '" . $search_term . "' or UPPER(Promo.description) like '%" . strtoupper($search_term) . "%'
                                        or Promo_Key_Word_Map.promo_key_word like '%" . strtoupper($search_term) . "%' 
                                        or Promo.start_date = '" . $search_term . "' or Promo.end_date = '" . $search_term . "')");
        }

        if ($active_only == 'true') {
            $query = $query->whereRaw("(Promo.active = 'Y')");
        }else if($active_only == 'false'){
            $query = $query->whereRaw("(Promo.active = 'N')");
        }

        if ($promo_type != -1) {
            $query = $query->whereRaw("(Promo.promo_type = $promo_type)");
        }

        return $query;
    }

    /**
     * @param        $user_id
     * @param string $filter
     * @param $active_only
     * @return Builder
     */
    public function getPromosForBrandManager($filter = "", $active_only = true, $promo_type = -1)
    {
        $promos = $this->applyFilter(
            $this->applyMerchantAndKeyMap($this->newQuery())
                ->where('Promo.active', '=', 'Y')
                ->where('Promo.brand_id', '=', session('brand_manager_brand')),
            $filter, $active_only, $promo_type)
            ->select(['Promo.promo_id', 'Promo_Key_Word_Map.promo_key_word', 'Promo.start_date', 'Promo.end_date', 'Promo.description', 'Promo.promo_type', 'Promo.active']);

        return $promos->distinct();
    }

    /**892
     * @param        $user_id
     * @param string $filter
     * @param $active_only
     * @return Builder
     */
    public function getPromosForOperator($user_id, $filter = "", $active_only = true, $promo_type = -1)
    {
        $promos = $this->applyFilter(
            $this->applyMerchantAndKeyMap($this->newQuery())
                ->join('portal_operator_merchant_map', 'Promo_Merchant_Map.merchant_id', '=', 'portal_operator_merchant_map.merchant_id')
                ->where('portal_operator_merchant_map.user_id', '=', $user_id),
            $filter, $active_only, $promo_type)
            ->select(['Promo.promo_id', 'Promo_Key_Word_Map.promo_key_word', 'Promo.start_date', 'Promo.end_date', 'Promo.description', 'Promo.promo_type', 'Promo.active']);
        return $promos->distinct();
    }

    /**
     * @param string $filter
     * @param $active_only
     * @return Builder
     */
    public function getPromosForGlobal($filter = "", $active_only = true, $promo_type = -1)
    {
        $promos = $this->applyFilter($this->newQuery()
            ->leftJoin('Promo_Key_Word_Map', 'Promo.promo_id', '=', 'Promo_Key_Word_Map.promo_id')
            ->leftJoin('smawv_promo_first_keyword', 'Promo.promo_id', '=', 'smawv_promo_first_keyword.promo_id'), $filter, $active_only, $promo_type)
            ->select(['Promo.promo_id', 'smawv_promo_first_keyword.promo_key_word',
                'Promo.start_date', 'Promo.end_date', 'Promo.description', 'Promo.promo_type', 'Promo.active']);
        return $promos->distinct();
    }

    /**
     * @param $user_owner_ids
     * @param $filter
     * @param $active_only
     * @return Builder
     */
    public function getPromosForMineOnly($user_owner_ids, $filter = "", $active_only = true, $promo_type = -1)
    {
        $promos = $this->applyFilter($this->newQuery()
            ->leftJoin('Promo_Key_Word_Map', 'Promo.promo_id', '=', 'Promo_Key_Word_Map.promo_id')
            ->leftJoin('smawv_promo_first_keyword', 'Promo.promo_id', '=', 'smawv_promo_first_keyword.promo_id')
            ->join('portal_object_ownership', 'Promo.promo_id', '=', 'portal_object_ownership.object_id')
            ->whereRaw('(portal_object_ownership.object_type = "promo" and portal_object_ownership.user_id in (' . $user_owner_ids . '))'),
            $filter, $active_only, $promo_type)
            ->select(['Promo.promo_id', 'smawv_promo_first_keyword.promo_key_word', 'Promo.start_date', 'Promo.end_date', 'Promo.description', 'Promo.promo_type', 'Promo.active']);
        $promos = $this->changePromoTypeToFullText($promos);
        return $promos->distinct();
    }

    /**
     * @param $user_organization_id
     * @param $filter
     * @param $active_only
     * @return Builder
     */
    public function getPromosForOrganizationAll($user_organization_id, $filter = "", $active_only = true, $promo_type = -1)
    {
        $promos = $this->applyFilter($this->newQuery()
            ->join('Promo_Key_Word_Map', 'Promo.promo_id', '=', 'Promo_Key_Word_Map.promo_id')
            ->join('smawv_promo_first_keyword', 'Promo.promo_id', '=', 'smawv_promo_first_keyword.promo_id')
            ->join('portal_object_ownership', 'Promo.promo_id', '=', 'portal_object_ownership.object_id')
            ->where('portal_object_ownership.object_type', '=', 'promo')
            ->where('portal_object_ownership.organization_id', '=', $user_organization_id), $filter, $active_only, $promo_type)
            ->select(['Promo.promo_id', 'smawv_promo_first_keyword.promo_key_word', 'Promo.start_date', 'Promo.end_date', 'Promo.description', 'Promo.promo_type', 'Promo.active']);

        return $promos->distinct();
    }

    public function changePromoTypeToFullText($promos) {
        $promo_types = $this->promo_types;
        foreach ($promos as $index=>$promo) {
            $promos[$index]->full_description = $promo->description;
            if (isset($promo_types[$promo->promo_type])) {
                $promos[$index]->promo_type = $promo_types[$promo->promo_type];
                if (strlen($promo->description) > 29) {
                    $promos[$index]->description = substr($promo->description, 0, 30)."...";
                }
            }
        }
        return $promos;
    }

    /***
     * Filter the promo query to lookup just the promos for the current day falls within the current start_date/end_date
     * @param Builder $query
     * @return Builder
     */
    public function filterRecordsForToday($query)
    {
        $current_day = Carbon::now();
        $query
            ->where('Promo.start_date', '<=', $current_day)
            ->where('Promo.end_date', '>=', $current_day);
        return $query;
    }

    public function getGlobalPromoMerchants($promo_id)
    {
        $merchants = DB::table('Merchant')
            ->join('Promo_Merchant_Map', 'Merchant.merchant_id', '=', 'Promo_Merchant_Map.merchant_id')
            ->where('Promo_Merchant_Map.promo_id', '=', $promo_id)
            ->select(['Promo.promo_id', 'Promo.promo_key_word', 'Promo.start_date', 'Promo.end_date', 'Promo.description', 'Promo.promo_type', 'Promo.active'])
            ->distinct();
        return $merchants;
    }

    public function getMineOnlyMerchants($promo_id)
    {
        $merchants = DB::table('Merchant')
            ->join('Promo_Merchant_Map', 'Merchant.merchant_id', '=', 'Promo_Merchant_Map.merchant_id')
            ->join('portal_object_ownership', 'Merchant.merchant_id', '=', 'portal_object_ownership.object_id')
            ->whereRaw('(portal_object_ownership.object_type = "merchant" and Promo_Merchant_Map.promo_id = ?')
            ->setBindings([$promo_id])
            ->distinct()
            ->get(['Merchant.merchant_id', 'Merchant.name', 'Promo_Merchant_Map.start_date', 'Promo_Merchant_Map.end_date', 'Promo_Merchant_Map.max_discount_per_order', 'Promo_Merchant_Map.map_id'])
            ->toArray();
        return $merchants;
    }

    /**
     * @param $visibility
     * @param string $order_by
     * @param $order_direction
     * @param $recordAmount
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getActivePromosPaginated($visibility, $order_by = 'promo_id', $recordAmount = 10, $order_direction = 'DES')
    {
        $promos = $this->getActivePromos($visibility, $order_by, $order_direction)->paginate($recordAmount);
        $promos = $this->changePromoTypeToFullText($promos);
        return $promos;
    }

    /**
     * @param $visibility
     * @param string $order_by
     * @param $order_direction
     * @return Builder
     */
    public function getActivePromos($visibility, $order_by = 'promo_id', $order_direction = 'DES')
    {
        /** @var IVisibility $promos */
        $promos = App::make('promos.' . $visibility);
        return $promos->getActivePromos()->orderBy($order_by, $order_direction);
    }
}
