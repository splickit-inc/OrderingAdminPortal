<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Model\Permission;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
//        foreach ($this->getPermissions() as $permission) {
//                Gate::define($permission->name, function($user) use ($permission) {
//                $user->hasRole($permission->roles);
//            });
//        }
    }

    protected function getPermissions() {
        return Permission::with('roles')->get();
    }

    public function hasRole() {

    }

}
