<?php

namespace App\BusinessModel\Visibility\Promos;


use App\BusinessModel\Authentication\Users;
use App\Model\Promo;

class Visibility implements IVisibility
{
    protected $promo;
    protected $current_user;

    public function __construct(Promo $promo, Users $users)
    {
        $this->promo = $promo;
        $this->current_user = $users->getUserAuthenticated();
    }


    /**
     * @return \Illuminate\Database\Eloquent\Builder|null
     * @throws \Exception
     */
    function getActivePromos()
    {
        return null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|null
     * @param $filter
     * @param $active_only
     * @throws \Exception
     */
    function searchForPromos($filter, $active_only, $promo_type)
    {
        return null;
    }
}