<?php

namespace App\BusinessModel\Visibility\Promos;


class VGlobal extends Visibility
{
    /**
     * @return \Illuminate\Database\Eloquent\Builder|null
     * @throws \Exception
     */
    function getActivePromos()
    {
        try {
            return $this->promo
                ->filterRecordsForToday($this->promo->getPromosForGlobal());
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    function searchForPromos($filter, $active_only = true, $promo_type = -1)
    {
        if ($this->current_user != null) {
            return $this->promo->getPromosForGlobal($filter, $active_only, $promo_type);
        }
        return null;
    }
}