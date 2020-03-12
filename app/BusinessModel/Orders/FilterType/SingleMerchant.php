<?php

namespace App\BusinessModel\Orders\FilterType;


use Illuminate\Database\Eloquent\Builder;

class SingleMerchant
{
    protected $merchant;

    public function __construct($merchant)
    {
        $this->merchant = $merchant;
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    function addFilterTypeToQuery($query)
    {
        return $query->where('Orders.merchant_id', '=', $this->merchant);
    }
}