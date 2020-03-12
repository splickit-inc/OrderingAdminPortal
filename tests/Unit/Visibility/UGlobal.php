<?php

namespace Tests\Unit\Visibility;


use App\User;

class UGlobal extends UserVisibility
{
    protected $name = 'global';

    /**
     * @inheritdoc
     */
    function configureUserForCurrentVisibility(User $user)
    {
        return $user;
    }
}