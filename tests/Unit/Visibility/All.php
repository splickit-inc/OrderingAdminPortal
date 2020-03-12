<?php

namespace Tests\Unit\Visibility;


use App\User;

class All extends UserVisibility
{
    protected $name = 'all';

    function configureUserForCurrentVisibility(User $user)
    {
        return $user;
    }
}