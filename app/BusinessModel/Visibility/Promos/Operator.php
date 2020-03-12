<?php

namespace App\BusinessModel\Visibility\Promos;


class Operator extends Visibility
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
                ->filterRecordsForToday($this->promo->getPromosForOperator($user_id));
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    function searchForPromos($filter, $active_only, $promo_type)
    {
        if ($this->current_user != null) {
            $user_id = $this->current_user->id;
            return $this->promo->getPromosForOperator($user_id, $filter, $active_only, $promo_type);
        }
        return null;
    }
}