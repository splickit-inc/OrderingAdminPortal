<?php

namespace App\BusinessModel\Orders\FilterType;


class Brand implements IFilterType
{
    protected $brand_id;

    public function __construct($brand_id)
    {
        $this->brand_id = $brand_id;
    }

    /** @inheritdoc */
    function addFilterTypeToQuery($query)
    {
        return $query->join('Merchant', 'Orders.merchant_id', '=', 'Merchant.merchant_id')
            ->where('Merchant.brand_id', '=', $this->brand_id);
    }
}