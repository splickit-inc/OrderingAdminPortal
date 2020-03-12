<?php namespace App\Model;

use App\BusinessModel\Visibility\Brands\IVisibility;
use App\BusinessModel\Visibility\Merchant\IMerchantVisibility;
use App\Model\Traits\BooleanHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\LogicalDelete;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

class Brand extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use BooleanHelper;

    protected $table = 'Brand2';
    protected $primaryKey = 'brand_id';

    protected $fillable = [
        'brand_name',
        'brand_external_identifier',
        'active',
        'gift_enabled',
        'contact_number_required',
        'allows_tipping',
        'allows_pricing_adjustments',
        'allows_in_store_payments',
        'nutrition_data_link',
        'nutrition_flag',
        'logical_delete',
        'support_email',
        'production',
        'last_orders_displayed',
        'external_identifier',
    ];

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new LogicalDelete);
    }

    public function loyaltyRule()
    {
        return $this->hasOne(BrandLoyaltyRules::class, 'brand_id', 'brand_id')->withDefault();
    }

    public function loyaltyBehaviourAwardAmount()
    {
        return $this->hasManyThrough(LoyaltyBrandBehaviorAwardAmountMap::class, LoyaltyAwardBrandTriggerAmounts::class, 'brand_id', 'loyalty_award_brand_trigger_amounts_id', 'brand_id');
    }

    public function loyaltyAwardTriggerAmounts()
    {
        return $this->hasMany(LoyaltyAwardBrandTriggerAmounts::class, 'brand_id', 'brand_id');
    }

    public function loyaltyAwardAmountMaps()
    {
        return $this->hasMany(LoyaltyBrandBehaviorAwardAmountMap::class, 'brand_id', 'brand_id');
    }

    /**
     * get the visibility query for the current logged user
     *
     * @return IVisibility
     */
    private
    function getVisibilityQuery()
    {
        return App::make('brands.' . session('user_visibility'));
    }

    /**
     * Creates a new Brand filling the basic fields with the Lead information
     *
     * @param Lead $lead
     * @return Brand
     */
    public
    static function fromLead(Lead $lead)
    {
        $brand = new Brand();
        $brand->fill(['brand_name' => $lead['store_name']]);
        return $brand;
    }

    /**
     * @param array $fields
     * @param       $search_value
     * @return Collection
     */
    public
    function searchBrands(array $fields, $search_value)
    {
        try {
            $visibility = $this->getVisibilityQuery();
            return $visibility->searchRecords($fields, $search_value)->get();
        } catch (\Exception $exception) {
            return new Collection();
        }

    }

    public
    function getByFirstLetter($letter)
    {
        try {
            $visibility = $this->getVisibilityQuery();
            return $visibility->getByFirstLetter($letter)->get();
        } catch (\Exception $exception) {
            return new Collection();
        }
    }

    public
    function allBrands()
    {
        try {
            $visibility = $this->getVisibilityQuery();
            return $visibility->getAllRecords()->get()->toArray();
        } catch (\Exception $exception) {
            return [];
        }
    }

    public
    function editBrand($brand_id, $data)
    {
        /** @var $model $this */
        $model = $this->find($brand_id);
        if ($model) {
            $model->fill($data);
            $model->save();
            return $model;
        }
        return null;
    }

    public
    function merchants($visibility = 'mine_only')
    {
        /** @var $visibilityFilter IMerchantVisibility */
        $visibilityFilter = App::make("merchant.$visibility");
        return $visibilityFilter->getMerchants()
            ->where('Merchant.brand_id', $this['brand_id'])
            ->orderBy('display_name')
            ->get(['name', 'merchant_id', 'name', 'display_name', 'address1', 'city', 'state',
                'phone_no', 'zip']);
    }

    public function setLoyaltyConfiguration(array $data)
    {
        try {
            $data = array_intersect_key($data, array_flip($this->loyaltyRule()->getRelated()->getFillable()));
            /** @var BrandLoyaltyRules $business_info */
            $loyalty_rule = $this->loyaltyRule()->first();
            if (empty($loyalty_rule)) {
                $this->loyaltyRule()->create($data);
            } else {
                $loyalty_rule->update($data);
            }
            return $this->loyaltyRule()->first();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function setBonusPointsDayConfiguration(array $data)
    {
        try {
            $data['loyalty_award_trigger_type_id'] = '1002';
            $data['process_type'] = 'multiplier';
            $data['brand_id'] = $this->brand_id;

            $data_trigger_amount = array_intersect_key($data, array_flip($this->loyaltyAwardTriggerAmounts()->getRelated()->getFillable()));
            $data_amount_map = array_intersect_key($data, array_flip($this->loyaltyAwardAmountMaps()->getRelated()->getFillable()));

            DB::beginTransaction();
            /** @var LoyaltyAwardBrandTriggerAmounts $trigger_amount */
            $trigger_amount = $this->loyaltyAwardTriggerAmounts()->where('trigger_value', '=', $data_trigger_amount['trigger_value'])->first();
            if (empty($trigger_amount)) {
                $trigger_amount = $this->loyaltyAwardTriggerAmounts()->create($data_trigger_amount);
            }

            $trigger_amount->loyaltyMap()->create($data_amount_map);
            $result = $this->loyaltyBehaviourAwardAmount()->get([
                'Loyalty_Award_Brand_Trigger_Amounts.trigger_value',
                'Loyalty_Award_Brand_Trigger_Amounts.loyalty_award_trigger_type_id',
                'Loyalty_Award_Brand_Trigger_Amounts.loyalty_award_trigger_type_id',
                'Loyalty_Brand_Behavior_Award_Amount_Maps.*'
            ]);
            DB::commit();
            return $result;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function deleteBonusPointsDayRecord($bonus_id)
    {
        try {
            DB::beginTransaction();
            /** @var LoyaltyBrandBehaviorAwardAmountMap $amount_map */
            $amount_map = $this->loyaltyAwardAmountMaps()->find($bonus_id);
            if (!empty($amount_map)) {
                $amount_map->delete();
            }
            $result = $this->loyaltyBehaviourAwardAmount()->get([
                'Loyalty_Award_Brand_Trigger_Amounts.trigger_value',
                'Loyalty_Award_Brand_Trigger_Amounts.loyalty_award_trigger_type_id',
                'Loyalty_Award_Brand_Trigger_Amounts.loyalty_award_trigger_type_id',
                'Loyalty_Brand_Behavior_Award_Amount_Maps.*'
            ]);
            DB::commit();
            return $result;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
