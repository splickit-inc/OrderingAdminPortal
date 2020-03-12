<?php

namespace App\Providers;

use App\BusinessModel\Visibility\Brands\All;
use App\BusinessModel\Visibility\Brands\Mine_only;
use App\BusinessModel\Visibility\Brands\Operator;
use App\BusinessModel\Visibility\Brands\VGlobal;
use App\BusinessModel\Visibility\Merchant;
use App\BusinessModel\Visibility\Promos;
use App\Model\MerchantVisibility\MerchantAll;
use App\Model\MerchantVisibility\MerchantGlobal;
use App\Model\MerchantVisibility\MerchantMineOnly;
use App\Model\MerchantVisibility\MerchantOperator;
use App\Model\PortalQueryLog;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!App::environment(['local'])) {
            $this->app['request']->server->set('HTTPS', 'on');
        }

        DB::connection('reports_db')->listen(function ($query) {
            if (strpos($query->sql, 'aggregate') !== false) {
                $portal_query_log = new PortalQueryLog();

                $portal_query_log->query = $query->sql;
                $portal_query_log->variables = json_encode($query->bindings);

                $portal_query_log->save();
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /**
         * Promos Visibility functionality for the DI container
         */
        $this->app->bind('promos.global', Promos\VGlobal::class);
        $this->app->bind('promos.mine_only', Promos\Mine_only::class);
        $this->app->bind('promos.operator', Promos\Operator::class);
        $this->app->bind('promos.all', Promos\All::class);
        $this->app->bind('promos.brand', Promos\Brand::class);

        /**
         * Brands Visibilities
         */
        $this->app->bind('brands.global', VGlobal::class);
        $this->app->bind('brands.mine_only', Mine_only::class);
        $this->app->bind('brands.operator', Operator::class);
        $this->app->bind('brands.all', All::class);
        /**
         * Merchant Visibilities
         */
        $this->app->bind('merchant.global', Merchant\MGlobal::class);
        $this->app->bind('merchant.mine_only', Merchant\Mine_only::class);
        $this->app->bind('merchant.operator', Merchant\Operator::class);
        $this->app->bind('merchant.all', Merchant\All::class);
        $this->app->bind('merchant.brand', Merchant\Brand::class);
    }
}
