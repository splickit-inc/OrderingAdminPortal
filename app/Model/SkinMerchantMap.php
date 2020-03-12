<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SkinMerchantMap extends Model implements Auditable  {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'Skin_Merchant_Map';
    protected $primaryKey = 'map_id';

    protected $fillable = ['skin_id', 'merchant_id', 'logical_delete'];

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    /**
     * @param $merchant_id
     * @param $brand_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getRelatedSkinsForMerchantBrand($merchant_id, $brand_id) {
        return $this->newQuery()
            ->join('Skin', 'Skin_Merchant_Map.skin_id', '=', 'Skin.skin_id')
            ->where('Skin_Merchant_Map.merchant_id', '=', $merchant_id)
            ->where('Skin.brand_id', '=', $brand_id)
            ->select(['Skin.*']);
    }

    /**
     * @param       $merchant_id
     * @param array $skins
     * @return array
     */
    public function createNewRelation($merchant_id, $skins) {
        $response = [];
        foreach ($skins as $skin) {
            if (static::where('merchant_id', '=', $merchant_id)->where('skin_id', '=', $skin['skin_id'])->count() == 0) {
                $response = array_push($response, static::updateOrCreate(
                    ['merchant_id' => $merchant_id],
                    ['skin_id' => $skin['skin_id']]
                ));
            }
        }
        return $response;
    }
}