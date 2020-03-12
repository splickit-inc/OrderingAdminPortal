<?php

namespace App\BusinessModel\Visibility\Promos;


class Mine_only extends Visibility
{
    /**
     * @return \Illuminate\Database\Eloquent\Builder|null
     * @throws \Exception
     */
    function getActivePromos()
    {
        try {
            $user_owner_ids = implode(', ', session('user_child_users'));
            return $this->promo
                ->filterRecordsForToday($this->promo->getPromosForMineOnly($user_owner_ids));
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    function searchForPromos($filter, $active_only, $promo_type)
    {
        if ($this->current_user != null) {
            $user_owner_ids = implode(', ', session('user_child_users'));
            return $this->promo->getPromosForMineOnly($user_owner_ids, $filter, $active_only, $promo_type);
        }
        return null;
    }
}