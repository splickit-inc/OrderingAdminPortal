<?php

namespace App\Providers;


use App\Model\EmailType;
use App\Service\UserService;
use Illuminate\Support\ServiceProvider;
use App\BusinessModel\Visibility\Users;

class UserServiceProvider extends ServiceProvider {
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        /**
         * Users Visibility functionality for the DI container
         */
        $this->app->bind('users.global',Users\UGlobal::class);
        $this->app->bind('users.mine_only',Users\Mine_only::class);
        $this->app->bind('users.operator',Users\Operator::class);
        $this->app->bind('users.all', Users\All::class);
        $this->app->bind('users.brand', Users\Brand::class);

        $this->app->when(UserService::class)
            ->needs(EmailType::class)
            ->give(function () {
                return EmailType::genericType();
            });
    }
}