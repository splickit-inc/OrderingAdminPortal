<?php

namespace App\BusinessModel\Visibility\Users;


use App\User;
use Illuminate\Support\Facades\App;

class All extends Visibility
{

    public function getAllUsers()
    {
        //User organization id
        /** @var User $user */
        $user = App::make(User::class);
        $result = $user->filterRoleHierarchy(
            $user->getAllUsers(session('user_organization_id')),
            session('user_roles')[0]
        )->distinct('email')->get()->toArray();
        return ['users' => $result];
    }
}