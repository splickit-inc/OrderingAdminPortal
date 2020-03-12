<?php

namespace App\BusinessModel\Visibility\Users;

use App\Model\Role;
use App\User;
use Illuminate\Support\Facades\App;

class UGlobal extends Visibility
{

    protected $role_model;

    public function __construct(Role $role_model)
    {
        $this->role_model = $role_model;
    }

    public function getAllUsers()
    {
        /** @var User $user */
        $user = App::make(User::class);
        $result = $user->filterRoleHierarchy(
            $user->getGlobalUsers(),
            session('user_roles')[0]
        )->distinct('email')->get()->toArray();
        $roles = $this->role_model->getCreateRoles();
        return ['users' => $result, 'roles' => $roles];
    }
}