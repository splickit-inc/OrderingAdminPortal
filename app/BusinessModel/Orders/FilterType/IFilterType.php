<?php

namespace App\BusinessModel\Orders\FilterType;


use Illuminate\Database\Eloquent\Builder;

interface IFilterType
{
    /**
     * @param Builder $query
     * @return Builder
     */
    function addFilterTypeToQuery($query);
}