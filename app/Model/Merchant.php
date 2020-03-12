<?php namespace App\Model;

use App\BusinessModel\Visibility\Merchant\IMerchantVisibility;
use App\Model\Traits\ArrayHelper;
use App\Model\Traits\BasicModel;
use App\Scopes\LogicalDelete;
use App\Service\Utility;
use function Couchbase\defaultDecoder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

class Merchant extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use BasicModel;

    protected $table = 'Merchant';
    protected $primaryKey = 'merchant_id';

    public $search_query;
    public $variable_bindings = [];

    protected $fillable = [
        'brand_id',
        'name',
        'address1',
        'address2',
        'city',
        'state',
        'zip',
        'country',
        'shop_email',
        'phone_no',
        'time_zone',
        'delivery',
        'show_tip',
        'advanced_ordering',
        'ordering_on',
        'group_ordering_on',
        'active',
        'delivery',
        'immediate_message_delivery',
        'inactive_reason',
        'show_tip',
        'lead_time',
        'created',
        'modified'
    ];

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new LogicalDelete);
    }

    public function paymentServiceBusiness()
    {
        return $this->hasOne(PaymentServiceBusiness::class, 'merchant_id', 'merchant_id')->withDefault();
    }

    public function paymentServiceOwner()
    {
        return $this->hasOne(PaymentMerchantApplication::class, 'merchant_id', 'merchant_id')->withDefault();
    }

    public function statements()
    {
        return $this->hasMany(TransactionStatement::class, 'merchant_id', 'merchant_id');
    }

    public function businessBanking()
    {
        return $this->hasOne(BusinessBanking::class, 'merchant_id', 'merchant_id');
    }

    public function businessInfo()
    {
        return $this->hasOne(BusinessInfo::class, 'merchant_id', 'merchant_id');
    }

    public function holeHours()
    {
        return $this->hasMany(HoleHours::class, 'merchant_id', 'merchant_id');
    }

    /**
     * Get all the menus related with this merchant
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function menu()
    {
        return $this->belongsToMany(Menu::class, 'Merchant_Menu_Map')->withPivot('map_id', 'merchant_menu_type', 'allows_dine_in_orders', 'allows_curbside_pickup');
    }

    public function cateringInfo()
    {
        return $this->hasOne(MerchantCateringInfo::class, 'merchant_id', 'merchant_id')->withDefault();
    }

    /**
     * @param $merchant_id
     * @return Model|null
     */
    public function getMerchantWithMenus($merchant_id)
    {
        $result = static::with('menu')
            ->where('merchant_id', '=', $merchant_id)->get()->first();
        if (count($result) > 0)
            return $result;
        return null;
    }

    public function getMerchantStatements($merchant_id, $order_by = 'adm_trans_statement.created', $order_direction = 'DES')
    {
        try {
            $result = $this->where('merchant_id', '=', $merchant_id)->first()->statements()->orderBy($order_by, $order_direction)
                ->select('*', DB::raw('CONCAT(CONCAT(SUBSTRING(period,-4),\', \'), REPLACE(period,CONCAT(\', \',SUBSTRING(period,-4)),\'\')) as periodByYear'));
            //->select('*', DB::raw('(promo_amt/2) as discount')); in case that we need to calculate something
            return $result;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Creates a new Merchant filling the basic fields with the Lead information
     *
     * @param Lead $lead
     * @param      $brandId
     * @return Merchant
     */
    public static function fromLead(Lead $lead, $brandId)
    {
        $merchant = new Merchant();
        $merchant->fill(['brand_id' => $brandId, 'name' => $lead['store_name'], 'address1' => $lead['store_address1'],
            'address2' => $lead['store_address2'], 'city' => $lead['store_city'], 'state' => $lead['store_state'],
            'zip' => $lead['store_zip'], 'country' => $lead['store_country'], 'shop_email' => $lead['store_email'],
            'phone_no' => $lead['store_phone_no'], 'time_zone' => $lead['store_time_zone']]);
        return $merchant;
    }

    /**
     * Sets the customer data to the merchant (used by merchant form 2)
     *
     * @param array $data
     * @return $this
     */
    public function setupCustomerData(array $data)
    {
        $utility = new Utility();
        $this->fill(['delivery' => $utility->convertBooleanYN($data['has_delivery']),
            'show_tip' => $utility->convertBooleanYN($data['allow_tipping']),
            'advanced_ordering' => $utility->convertBooleanYN($data['allow_advanced_orders']),
            'group_ordering_on' => $data['allow_group_orders'] ? 1 : 0]);
        return $this;
    }

    public function searchWhereClause($search_text)
    {
        $search_terms = explode(" ", $search_text);

        $this->variable_bindings = [];

        foreach ($search_terms as $search_term) {
            $this->search_query = $this->search_query->whereRaw("((Merchant.merchant_id = ? and Merchant.merchant_id > 0) or (UPPER(merchant_external_id) = ?) 
            or (replace(replace(replace(replace(Merchant.phone_no,' ',''),'(',''),')',''),'-','') like ? and length(Merchant.phone_no) > 5)
            or UPPER(name) like ? or UPPER(display_name) like ? or UPPER(CONCAT(address1, ', ', city, ', ',state, ' ',zip)) like ? or (numeric_id = ? and numeric_id > 0) 
            or (merchant_user_id = ? and merchant_user_id > 0) 
            or (Merchant.state = ?)
            or (EIN_SS = ? and EIN_SS > 0) or UPPER(shop_email) like ?)");

            array_push($this->variable_bindings, $search_term, $search_term, preg_replace("/[^0-9,.]/", "", "%" . $search_term . "%"), '%' . strtoupper($search_term) . "%",
                "%" . strtoupper($search_term) . "%", '%' . strtoupper($search_term) . "%", $search_term, $search_term, $search_term,$search_term,
                '%' . strtoupper($search_term) . "%");
        }

        $this->search_query = $this->search_query->whereRaw("Merchant.logical_delete = 'N'");
    }

    public function searchGlobal($search_text, $order_column = 'Merchant.modified', $order_direction = 'DES')
    {
        $this->search_query = DB::table('Merchant');

        $this->searchWhereClause($search_text);

        $this->search_query->setBindings($this->variable_bindings);

        $this->search_query->orderBy($order_column, $order_direction);

        return $this->search_query;
    }

    public function searchOrganizationAll($search_text, $order_column = 'Merchant.modified', $order_direction = 'DES')
    {
        $this->search_query = DB::table('Merchant')
            ->join('portal_object_ownership', 'Merchant.merchant_id', '=', 'portal_object_ownership.object_id');

        $this->searchWhereClause($search_text);

        $this->search_query = $this->search_query->whereRaw("(portal_object_ownership.object_type = 'merchant' and portal_object_ownership.organization_id = ?) and (logical_delete = 'N')");

        array_push($this->variable_bindings, session('user_organization_id'));

        $this->search_query->setBindings($this->variable_bindings);

        $this->search_query->orderBy($order_column, $order_direction);

        return $this->search_query;
    }

    public function searchMineOnly($search_text, $order_column = 'Merchant.modified', $order_direction = 'DES')
    {
        $this->search_query = DB::table('Merchant')
            ->join('portal_object_ownership', 'Merchant.merchant_id', '=', 'portal_object_ownership.object_id');

        $this->searchWhereClause($search_text);

        $user_owner_ids = implode(', ', session('user_child_users'));

        $this->search_query = $this->search_query->whereRaw("(portal_object_ownership.object_type = 'merchant' and portal_object_ownership.user_id in (?)) and (logical_delete = 'N')");

        array_push($this->variable_bindings, $user_owner_ids);

        $this->search_query->setBindings($this->variable_bindings);

        $this->search_query->orderBy($order_column, $order_direction);

        return $this->search_query;
    }

    public function searchOperator($search_text, $order_column = 'Merchant.modified', $order_direction = 'DES')
    {
        $this->search_query = DB::table('Merchant')
            ->join('portal_operator_merchant_map', 'Merchant.merchant_id', '=', 'portal_operator_merchant_map.merchant_id');

        $this->searchWhereClause($search_text);

        $this->search_query = $this->search_query->whereRaw("(portal_operator_merchant_map.user_id = ?) and (logical_delete = 'N')");

        array_push($this->variable_bindings, Auth::user()->id);

        $this->search_query->setBindings($this->variable_bindings);

        $this->search_query->orderBy($order_column, $order_direction);

        return $this->search_query;
    }

    public function searchBrand($search_text, $order_column = 'Merchant.modified', $order_direction = 'DES')
    {
        $this->search_query = DB::table('Merchant')
            ->join('Brand2', 'Merchant.brand_id', '=', 'Brand2.brand_id')
            ->join('portal_brand_manager_brand_map', 'Merchant.brand_id', '=', 'portal_brand_manager_brand_map.brand_id');

        $this->searchWhereClause($search_text);

        $this->search_query = $this->search_query->whereRaw("(portal_brand_manager_brand_map.user_id = ?)");

        array_push($this->variable_bindings, Auth::user()->id);

        $this->search_query->setBindings($this->variable_bindings);

        $this->search_query->orderBy($order_column, $order_direction);

        return $this->search_query;
    }

    public function firstLetterFilterSuperUser($letter, $order_by = 'Merchant.name', $order_direction = 'DES')
    {
        $merchants = Merchant::whereRaw("LEFT(UPPER(name) , 1) = ?")
            ->orderBy($order_by, $order_direction)
            ->setBindings([$letter]);
        return $merchants;
    }

    public function firstLetterFilterMineOnly($letter, $order_by = 'Merchant.name', $order_direction = 'DES')
    {
        $user_owner_ids = implode(', ', session('user_child_users'));

        $merchants = DB::table('Merchant')
            ->join('portal_object_ownership', 'Merchant.merchant_id', '=', 'portal_object_ownership.object_id')
            ->whereRaw('(portal_object_ownership.object_type = "merchant" and portal_object_ownership.user_id in (?)) and (LEFT(UPPER(Merchant.name) , 1) = ?)')
            ->orderBy($order_by, $order_direction)
            ->setBindings([$user_owner_ids, $letter])
            ->distinct();
        return $merchants;
    }

    public function firstLetterFilterOrganizationAll($letter, $order_by = 'Merchant.name', $order_direction = 'DES')
    {
        $merchants = DB::table('Merchant')
            ->join('portal_object_ownership', 'Merchant.merchant_id', '=', 'portal_object_ownership.object_id')
            ->whereRaw('(portal_object_ownership.object_type = "merchant" and portal_object_ownership.organization_id = ?) and (LEFT(UPPER(Merchant.name) , 1) = ?)')
            ->orderBy($order_by, $order_direction)
            ->setBindings([session('user_organization_id'), $letter])
            ->distinct();
        return $merchants;
    }

    public function firstLetterFilterOperator($letter, $order_by = 'Merchant.name', $order_direction = 'DES')
    {
        $merchants = DB::table('Merchant')
            ->join('portal_operator_merchant_map', 'Merchant.merchant_id', '=', 'portal_operator_merchant_map.merchant_id')
            ->whereRaw('portal_operator_merchant_map.user_id = ? and (LEFT(UPPER(Merchant.name) , 1) = ?)')
            ->orderBy($order_by, $order_direction)
            ->setBindings([Auth::user()->id, $letter])
            ->distinct();
        return $merchants;
    }

    public function firstLetterFilterBrand($letter, $order_by = 'Merchant.name', $order_direction = 'DES')
    {
        $merchants = DB::table('Merchant')
            ->join('Brand2', 'Merchant.brand_id', '=', 'Brand2.brand_id')
            ->join('portal_brand_manager_brand_map', 'Brand2.brand_id', '=', 'portal_brand_manager_brand_map.brand_id')
            ->whereRaw('portal_brand_manager_brand_map.user_id = ? and (LEFT(UPPER(Merchant.name) , 1) = ?)')
            ->orderBy($order_by, $order_direction)
            ->setBindings([Auth::user()->id, $letter])
            ->distinct();
        return $merchants;
    }

    public function searchMenu($search_text, $menu_id)
    {
        $merchants = DB::table('Merchant')
            ->join('Merchant_Menu_Map', 'Merchant.merchant_id', '=', 'Merchant_Menu_Map.merchant_id');

        if (intval($search_text) != 0) {
            $merchants = $merchants
                ->whereRaw('(Merchant.merchant_id = ? or Merchant.merchant_external_id = ?) and (Merchant.logical_delete = "N") and Merchant_Menu_Map.menu_id = ?')
                ->setBindings([$search_text, $search_text, $menu_id]);
        } else {
            $merchants = $merchants
                ->whereRaw("(name like ? or display_name like ? or CONCAT(address1, ', ', city, ', ',state, ' ',zip ) like ? or merchant_external_id = ?) 
                            and Merchant.logical_delete = 'N' and Merchant_Menu_Map.menu_id = ?")
                ->setBindings(['%' . $search_text . "%", '%' . $search_text . "%", '%' . $search_text . "%", $search_text, $menu_id]);
        }

        return $merchants->distinct()
            ->get(['name', 'Merchant.merchant_id', 'name', 'address1', 'city', 'state', 'phone_no'])
            ->toArray();
    }

    public function pageLoadGlobal()
    {
        $merchants = Merchant::where('logical_delete', '=', 'N')
            ->take(100)
            ->get()
            ->sortByDesc('modified')
            ->toArray();
        return $merchants;
    }

    public function pageLoadAll()
    {
        $merchants = DB::table('Merchant')
            ->join('portal_object_ownership', 'Merchant.merchant_id', '=', 'portal_object_ownership.object_id')
            ->whereRaw('portal_object_ownership.object_type = "merchant" and portal_object_ownership.organization_id = ?')
            ->distinct()
            ->setBindings([session('user_organization_id')])
            ->orderBy('Merchant.modified', 'desc')
            ->get(['name', 'merchant_id', 'name', 'address1', 'city', 'state', 'phone_no'])
            ->toArray();
        return $merchants;
    }

    public function pageLoadMineOnly()
    {
        $user_owner_ids = implode(', ', session('user_child_users'));

        $merchants = DB::table('Merchant')
            ->join('portal_object_ownership', 'Merchant.merchant_id', '=', 'portal_object_ownership.object_id')
            ->whereRaw('portal_object_ownership.object_type = "merchant" and portal_object_ownership.user_id in (?)')
            ->distinct()
            ->setBindings([$user_owner_ids])
            ->orderBy('Merchant.modified', 'desc')
            ->get(['name', 'merchant_id', 'name', 'address1', 'city', 'state', 'phone_no'])
            ->toArray();
        return $merchants;
    }

    public function pageLoadBrand()
    {
        $merchants = DB::table('Merchant')
            ->join('Brand2', 'Merchant.brand_id', '=', 'Brand2.brand_id')
            ->join('portal_brand_manager_brand_map', 'Merchant.brand_id', '=', 'portal_brand_manager_brand_map.brand_id')
            ->whereRaw('(portal_brand_manager_brand_map.user_id = ?) and Merchant.logical_delete = "N"')
            ->setBindings([Auth::user()->id])
            ->take(100)
            ->distinct()
            ->orderBy('Merchant.modified', 'desc')
            ->get(['name', 'merchant_id', 'name', 'address1', 'city', 'state', 'phone_no'])
            ->toArray();
        return $merchants;
    }

    public function pageLoadOperator()
    {
        $merchants = DB::table('Merchant')
            ->join('portal_operator_merchant_map', 'Merchant.merchant_id', '=', 'portal_operator_merchant_map.merchant_id')
            ->where('portal_operator_merchant_map.merchant_id', '=', Auth::user()->id)
            ->take(100)
            ->distinct()
            ->orderBy('Merchant.modified', 'desc')
            ->get(['name', 'Merchant.merchant_id', 'name', 'address1', 'city', 'state', 'phone_no'])
            ->toArray();
        return $merchants;
    }

    /**
     * get the visibility query for the current logged user
     *
     * @return IMerchantVisibility
     */
    private function getVisibilityQuery()
    {
        return App::make('merchant.' . session('user_visibility'));
    }

    /**
     * @param array $fields
     * @param       $search_value
     * @return Collection
     */
    public function searchMerchantsByMenu(array $fields, $search_value)
    {
        try {
            /** @var IMerchantVisibility $visibility */
            $visibility = $this->getVisibilityQuery();
            return $visibility->searchMerchantsByMenu($fields, $search_value)->get();
        } catch (\Exception $exception) {
            return new Collection();
        }

    }

    /**
     * @param array $fields
     * @param $search_value
     * @param $orderBy
     * @return Builder
     * @throws \Exception
     */
    public function searchMerchants(array $fields, $search_value, $orderBy = 'Merchant.merchant_id')
    {
        try {
            /** @var IMerchantVisibility $visibility */
            $visibility = $this->getVisibilityQuery();
            return $visibility->searchMerchants($fields, $search_value)
                ->orderBy($orderBy);
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }

    /**
     * @param array $fields
     * @param $search_value
     * @param $brand_id
     * @param $orderBy
     * @return Builder
     * @throws \Exception
     */
    public function searchMerchantsByBrand(array $fields, $search_value, $brand_id, $orderBy = 'Merchant.merchant_id')
    {
        try {
            /** @var IMerchantVisibility $visibility */
            $visibility = $this->getVisibilityQuery();
            return $visibility->searchMerchants($fields, $search_value)
                ->where('Merchant.brand_id', '=', $brand_id)
                ->orderBy($orderBy);
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }

    /**
     * @return Collection
     */
    public function getMerchantsByMenu()
    {
        try {
            /** @var IMerchantVisibility $visibility */
            $visibility = $this->getVisibilityQuery();
            return $visibility->getMerchantsByMenu()->get();
        } catch (\Exception $exception) {
            return new Collection();
        }

    }


    public function updatePaymentBusinessInformation(array $data)
    {
        try {
            $data = array_intersect_key($data, array_flip($this->paymentServiceBusiness()->getRelated()->getFillable()));
            $business_info = $this->paymentServiceBusiness()->first();
            if (empty($business_info)) {
                $this->paymentServiceBusiness()->create($data);
            } else {
                $this->paymentServiceBusiness()->update($data);
            }
            return $this->paymentServiceBusiness()->first();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function updatePaymentOwnerInformation(array $data)
    {
        try {
            $data = array_intersect_key($data, array_flip($this->paymentServiceOwner()->getRelated()->getFillable()));
            $owner_info = $this->paymentServiceOwner()->first();
            if (empty($owner_info)) {
                $this->paymentServiceOwner()->create($data);
            } else {
                $this->paymentServiceOwner()->update($data);
            }
            return $this->paymentServiceOwner()->first();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function updateBusinessInfo(array $data)
    {
        try {
            $data = array_intersect_key($data, array_flip($this->businessInfo()->getRelated()->getFillable()));
            $business_info = $this->businessInfo()->first();
            if (empty($business_info)) {
                $this->businessInfo()->create($data);
            } else {
                $this->businessInfo()->update($data);
            }
            return $this->businessInfo()->first();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function updateBusinessBanking(array $data)
    {
        try {
            $data = array_intersect_key($data, array_flip($this->businessBanking()->getRelated()->getFillable()));
            $business_info = $this->businessBanking()->first();
            if (empty($business_info)) {
                $this->businessBanking()->create($data);
            } else {
                $this->businessBanking()->update($data);
            }
            return $this->businessBanking()->first();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function updateCateringInfo(array $data)
    {
        try {
            $data = array_intersect_key($data, array_flip($this->cateringInfo()->getRelated()->getFillable()));
            /** @var MerchantCateringInfo $business_info */
            $business_info = $this->cateringInfo()->first();
            if (empty($business_info)) {
                $this->cateringInfo()->create($data);
            } else {
                $business_info->update($data);
            }
            return $this->cateringInfo()->first();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param array $data
     * @return Model
     * @throws \Exception
     */
    public function addOrUpdateMidDayHour(array $data)
    {
        try {
            if (!empty($data['start_time'])) {
                $data['start_time'] = date("H:i:s", strtotime($data['start_time']));
            }
            if (!empty($data['end_time'])) {
                $data['end_time'] = date("H:i:s", strtotime($data['end_time']));
            }
            $data = array_intersect_key($data, array_flip($this->holeHours()->getRelated()->getFillable()));
            if (!empty($data['id'])) {
                return $this->holeHours()->updateOrCreate(['id' => $data['id']], $data);
            }
            return $this->holeHours()->create($data);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function removeMidDayHour($hour_id)
    {
        try {
            /** @var Model $model */
            $model = $this->holeHours()->where('id', '=', $hour_id)->first();
            if (!empty($model)) {
                return $model->delete();
            }
            throw new \Exception('Not Found', 404);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
