<?php
namespace App\BusinessModel\Visibility\promos;


use Illuminate\Database\Eloquent\Builder;

interface IVisibility {
    /**
     * Get all the Active promos for the logged user visibility
     * @return Builder | null
     */
    function getActivePromos();

    /**
     * Search for all promos for the logged user visibility and the set filter.
     * @param $filter
     * @param $active_only
     * @return Builder | null
     */
    function searchForPromos($filter, $active_only, $promo_type);
}