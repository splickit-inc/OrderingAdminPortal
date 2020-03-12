<?php

namespace App\BusinessModel\Orders;


use App\BusinessModel\Orders\FilterType\Brand;
use App\BusinessModel\Orders\FilterType\IFilterType;
use App\BusinessModel\Orders\FilterType\MultipleMerchant;
use App\BusinessModel\Orders\FilterType\SingleMerchant;

class ReportFactory
{
    /**
     * @param array $data
     * @return IFilterType
     */
    static function getFilterType(array $data)
    {
        try {
            if (!empty($data['brand_id'])) {
                return \App::make(Brand::class, ['brand_id' => $data['brand_id']]);
            }
            if (!empty($data['merchants'])) {
                return \App::make(MultipleMerchant::class, ['merchants' => $data['merchants']]);
            }

            $merchant = session('current_merchant_id');
            if (!empty($merchant)) {
                return \App::make(SingleMerchant::class, ['merchant' => $merchant]);
            }
            return null;
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return null;
        }
    }
}