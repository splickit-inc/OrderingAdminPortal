<?php

namespace Tests\Unit\Visibility;


use App\User;

class MineOnly extends UserVisibility
{
    protected $name = 'mine_only';

    function configureUserForCurrentVisibility(User $user)
    {
        return $user;
    }

    function getLoginAttemptResponseSpecificStructure()
    {
        $basicStructure = parent::getLoginAttemptResponseSpecificStructure();
        return array_merge($basicStructure, [
            'user_child_users'
        ]);
    }

    function getLoginAttemptResponseSpecificData(User $user)
    {
        $basicData = parent::getLoginAttemptResponseSpecificData($user);
        return array_merge($basicData, [
            'user_child_users' => [
                $user->id
            ]
        ]);
    }

    function getSessionInfoResponseSpecificStructure()
    {
        $basicStructure = parent::getSessionInfoResponseSpecificStructure();
        return array_merge($basicStructure, [
            'user_related_data' => [
                'user_child_users'
            ]
        ]);
    }
}