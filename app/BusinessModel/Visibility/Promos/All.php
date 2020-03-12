<?php

namespace App\BusinessModel\Visibility\Promos;


class All extends Visibility
{
    /**
     * @return \Illuminate\Database\Eloquent\Builder|null
     * @throws \Exception
     */
    function getActivePromos()
    {
        try {
            return $this->promo
                ->filterRecordsForToday($this->promo->getPromosForOrganizationAll(session('user_organization_id')));
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    function searchForPromos($filter, $active_only, $promo_type = -1)
    {
        if ($this->current_user != null) {
            return $this->promo->getPromosForOrganizationAll(session('user_organization_id'), $filter, $active_only, $promo_type);
        }
        return null;
    }
}