<?php

namespace App\BusinessModel\Visibility\Promos;


class Brand extends Visibility
{
    /**
     * @return \Illuminate\Database\Eloquent\Builder|null
     * @throws \Exception
     */
    function getActivePromos()
    {
        try {
            $user_id = $this->current_user->id;
            return $this->promo
                ->filterRecordsForToday($this->promo->getPromosforBrandManager($user_id));
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    function searchForPromos($filter, $active_only, $promo_type)
    {
        if ($this->current_user != null) {
            return $this->promo->getPromosforBrandManager($filter, $active_only, $promo_type);
        }
        return null;
    }
}