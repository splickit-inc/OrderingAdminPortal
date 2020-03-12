<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class Merchant_vivonet_info_Maps extends Model {

    protected $table = 'Merchant_Vivonet_Info_Maps';

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'merchant_id',
        'store_id',
        'merchant_key',
        'tender_id',
        'service_tip_id',
        'alternate_url',
        'promo_charge_id',
        'logical_delete'
    ];

    /**
     * @param array $data
     * @return Merchant_vivonet_info_Maps
     */
    public function createOrderingConfiguration(array $data) {
        return $this->updateOrCreate(
            ['merchant_id' => $data['merchant_id']],
            $data
        );
    }

    public function getMerchantVivonet($merchant_id) {
        return $this->where('merchant_id', '=', $merchant_id)->first();
    }

    /**
     * @param $merchant_id
     * @return int
     */
    public function deleteWithMerchantID($merchant_id)
    {
        $vivonet = $this->where('merchant_id', '=', $merchant_id)->first();
        if($vivonet)
        {
            return $vivonet->delete();
        }
        return 0;
    }
}