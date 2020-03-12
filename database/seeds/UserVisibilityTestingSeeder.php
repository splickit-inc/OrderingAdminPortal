<?php

use Illuminate\Database\Seeder;

class UserVisibilityTestingSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        //region Users
        DB::table('portal_users')
            ->insert(['id' => 5000, 'first_name' => 'Will', 'last_name' => 'Smith',
                'email' => 'wsmith@trueforce.com', 'password' => bcrypt('Splickit456%!'),
                'visibility' => 'all', 'organization_id' => 1]);
        DB::table('portal_users')
            ->insert(['id' => 5001, 'first_name' => 'Williams The Second', 'last_name' => 'Smith',
                'email' => 'wtssmith@trueforce.com', 'password' => bcrypt('Splickit456%!'),
                'visibility' => 'all', 'organization_id' => 1]);

        DB::table('portal_users')
            ->insert(['id' => 5002, 'first_name' => 'John', 'last_name' => 'Collins',
                'email' => 'jcollins@trueforce.com', 'password' => bcrypt('Splickit456%!'),
                'visibility' => 'mine_only', 'organization_id' => 2]);

        DB::table('portal_users')
            ->insert(['id' => 5003, 'first_name' => 'Michael', 'last_name' => 'Harris',
                'email' => 'mharris@trueforce.com', 'password' => bcrypt('Splickit456%!'),
                'visibility' => 'operator', 'organization_id' => 3]);
        //endregion

        //region Reseller Level Objects for All
        //region Brands
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5001, 'organization_id' => 1, 'object_type' => 'brand',
                'object_id' => 150]);// Snarf's
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5001, 'organization_id' => 1, 'object_type' => 'brand',
                'object_id' => 152]); // Smiling Moose Deli
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5001, 'organization_id' => 1, 'object_type' => 'brand',
                'object_id' => 282]); // Pita Pit
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5001, 'organization_id' => 1, 'object_type' => 'brand',
                'object_id' => 307]);// ElPolloLoco
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5001, 'organization_id' => 1, 'object_type' => 'brand',
                'object_id' => 436]); // Hollywood Bowl
        //endregion

        //region Merchants
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5001, 'organization_id' => 1, 'object_type' => 'merchant',
                'object_id' => 101077]); // Sunrise
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5001, 'organization_id' => 1, 'object_type' => 'merchant',
                'object_id' => 101265]); // Buford
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5001, 'organization_id' => 1, 'object_type' => 'merchant',
                'object_id' => 102403]); // Portland
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5001, 'organization_id' => 1, 'object_type' => 'merchant',
                'object_id' => 103894]); // El Burrito
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5001, 'organization_id' => 1, 'object_type' => 'merchant',
                'object_id' => 104704]); // Jacksonville Forsyth
        //endregion

        //region Menus
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5001, 'organization_id' => 1, 'object_type' => 'menu',
                'object_id' => 102505]); // Red Rocks Bar
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5001, 'organization_id' => 1, 'object_type' => 'menu',
                'object_id' => 102512]);// Snarfburger
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5001, 'organization_id' => 1, 'object_type' => 'menu',
                'object_id' => 1117]);// Smilling Moose Deli
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5001, 'organization_id' => 1, 'object_type' => 'menu',
                'object_id' => 102493]);// East Coast Wings
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5001, 'organization_id' => 1, 'object_type' => 'menu',
                'object_id' => 102578]);// Tokyo Joes AZ
        //endregion

        //region Promos
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5001, 'organization_id' => 1, 'object_type' => 'promo',
                'object_id' => 81]); // 50% off your order
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5001, 'organization_id' => 1, 'object_type' => 'promo',
                'object_id' => 107]); // Free Pita
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5001, 'organization_id' => 1, 'object_type' => 'promo',
                'object_id' => 533]); // Buy two pitas and a drink get one pita free
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5001, 'organization_id' => 1, 'object_type' => 'promo',
                'object_id' => 630]); // Free burrito or bowl with purchase of large drink
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5001, 'organization_id' => 1, 'object_type' => 'promo',
                'object_id' => 864]); // Save 10% off Your Online Catering Order! - 105446,105066,103501,102753
        //endregion

        //endregion

        //region Mine Only Level Objects

        //region Child users
        DB::table('portal_users')
            ->insert(['id' => 5004, 'first_name' => 'James', 'last_name' => 'Collins',
                'email' => 'jacollins@trueforce.com', 'password' => bcrypt('Splickit456%!'),
                'visibility' => 'mine_only', 'organization_id' => 2]);
        DB::table('portal_user_parent_child')
            ->insert(['user_id'=>5004, 'parent_id'=>5002]);

        DB::table('portal_users')
            ->insert(['id' => 5005, 'first_name' => 'Marie', 'last_name' => 'Collins',
                'email' => 'mcollins@trueforce.com', 'password' => bcrypt('Splickit456%!'),
                'visibility' => 'mine_only', 'organization_id' => 2]);
        DB::table('portal_user_parent_child')
            ->insert(['user_id'=>5005, 'parent_id'=>5002]);

        DB::table('portal_users')
            ->insert(['id' => 5006, 'first_name' => 'Heidi', 'last_name' => 'Collins',
                'email' => 'hcollins@trueforce.com', 'password' => bcrypt('Splickit456%!'),
                'visibility' => 'mine_only', 'organization_id' => 2]);
        DB::table('portal_user_parent_child')
            ->insert(['user_id'=>5006, 'parent_id'=>5002]);
        //endregion

        //region Grand Child users
        DB::table('portal_users')
            ->insert(['id' => 5007, 'first_name' => 'James Jr', 'last_name' => 'Collins',
                'email' => 'jajrcollins@trueforce.com', 'password' => bcrypt('Splickit456%!'),
                'visibility' => 'mine_only', 'organization_id' => 2]);
        DB::table('portal_user_parent_child')
            ->insert(['user_id'=>5007, 'parent_id'=>5004]);

        DB::table('portal_users')
            ->insert(['id' => 5008, 'first_name' => 'Marie Jr', 'last_name' => 'Higgins',
                'email' => 'mjrhiggins@trueforce.com', 'password' => bcrypt('Splickit456%!'),
                'visibility' => 'mine_only', 'organization_id' => 2]);
        DB::table('portal_user_parent_child')
            ->insert(['user_id'=>5008, 'parent_id'=>5005]);

        DB::table('portal_users')
            ->insert(['id' => 5009, 'first_name' => 'Heidi Jr', 'last_name' => 'Johnson',
                'email' => 'hjrjohnson@trueforce.com', 'password' => bcrypt('Splickit456%!'),
                'visibility' => 'mine_only', 'organization_id' => 2]);
        DB::table('portal_user_parent_child')
            ->insert(['user_id'=>5009, 'parent_id'=>5006]);
        //endregion

        //region Brands
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5004, 'organization_id' => 2, 'object_type' => 'brand',
                'object_id' => 162]);// Illegal Pete's
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5004, 'organization_id' => 2, 'object_type' => 'brand',
                'object_id' => 166]);// Saxy's Cafe
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5005, 'organization_id' => 2, 'object_type' => 'brand',
                'object_id' => 168]);// Great Harvest Bread Co.
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5005, 'organization_id' => 2, 'object_type' => 'brand',
                'object_id' => 174]);// Tully's Coffee
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5006, 'organization_id' => 2, 'object_type' => 'brand',
                'object_id' => 182]);// Subway Colorado
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5006, 'organization_id' => 2, 'object_type' => 'brand',
                'object_id' => 186]);// Brothers BBQ
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5007, 'organization_id' => 2, 'object_type' => 'brand',
                'object_id' => 198]);// Juice It Up!
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5007, 'organization_id' => 2, 'object_type' => 'brand',
                'object_id' => 200]);// Tokyo Joe's
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5008, 'organization_id' => 2, 'object_type' => 'brand',
                'object_id' => 202]);// The Coffee Bean & Tea Leaf
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5008, 'organization_id' => 2, 'object_type' => 'brand',
                'object_id' => 238]);// Gino's Pizza & Spaghetti House
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5009, 'organization_id' => 2, 'object_type' => 'brand',
                'object_id' => 244]);// Boulder Absinthe House
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5009, 'organization_id' => 2, 'object_type' => 'brand',
                'object_id' => 256]);// Giovanni's Pizza
        //endregion

        //region Merchants
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5004, 'organization_id' => 2, 'object_type' => 'merchant',
                'object_id' => 100939]); // Dothan on East Main street
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5004, 'organization_id' => 2, 'object_type' => 'merchant',
                'object_id' => 101133]); // University Park
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5005, 'organization_id' => 2, 'object_type' => 'merchant',
                'object_id' => 101299]); // Normal
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5005, 'organization_id' => 2, 'object_type' => 'merchant',
                'object_id' => 102077]); // Craigsville
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5006, 'organization_id' => 2, 'object_type' => 'merchant',
                'object_id' => 102407]); // Ithaca
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5006, 'organization_id' => 2, 'object_type' => 'merchant',
                'object_id' => 103495]); // 1448 Indian Trail
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5007, 'organization_id' => 2, 'object_type' => 'merchant',
                'object_id' => 105070]); // Oregon City
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5007, 'organization_id' => 2, 'object_type' => 'merchant',
                'object_id' => 105502]); // Northlake Mall
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5008, 'organization_id' => 2, 'object_type' => 'merchant',
                'object_id' => 105558]); // Meyer Park
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5008, 'organization_id' => 2, 'object_type' => 'merchant',
                'object_id' => 106297]); // Diamond Bar
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5009, 'organization_id' => 2, 'object_type' => 'merchant',
                'object_id' => 106478]); // Charleston Lv
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5009, 'organization_id' => 2, 'object_type' => 'merchant',
                'object_id' => 106534]); // Quincy - Central Ave South
        //endregion

        //region Menus
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5004, 'organization_id' => 2, 'object_type' => 'menu',
                'object_id' => 102429]); // Fresh Healthy Cafe Master Menu
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5004, 'organization_id' => 2, 'object_type' => 'menu',
                'object_id' => 102430]); // Your Pie Takeout Master Menu
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5005, 'organization_id' => 2, 'object_type' => 'menu',
                'object_id' => 102431]); // Snarfs Master Colorado
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5005, 'organization_id' => 2, 'object_type' => 'menu',
                'object_id' => 102444]); // The Office 842
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5006, 'organization_id' => 2, 'object_type' => 'menu',
                'object_id' => 102528]); // Burnt Toast
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5006, 'organization_id' => 2, 'object_type' => 'menu',
                'object_id' => 102584]); // Amici's La Jolla Delivery
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5007, 'organization_id' => 2, 'object_type' => 'menu',
                'object_id' => 102594]); // Masterpiece Deli
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5007, 'organization_id' => 2, 'object_type' => 'menu',
                'object_id' => 102604]); // EBA's menu
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5008, 'organization_id' => 2, 'object_type' => 'menu',
                'object_id' => 102624]); // Chronic Tacos
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5008, 'organization_id' => 2, 'object_type' => 'menu',
                'object_id' => 102636]); // POSITALIA
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5009, 'organization_id' => 2, 'object_type' => 'menu',
                'object_id' => 102708]); // Broadcom
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5009, 'organization_id' => 2, 'object_type' => 'menu',
                'object_id' => 102713]); // Vivonet Certification Menu
        //endregion

        //region Promos
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5004, 'organization_id' => 2, 'object_type' => 'promo',
                'object_id' => 17]); // brueggers
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5004, 'organization_id' => 2, 'object_type' => 'promo',
                'object_id' => 246]); // 1389206517
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5005, 'organization_id' => 2, 'object_type' => 'promo',
                'object_id' => 283]); // 1396391063
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5005, 'organization_id' => 2, 'object_type' => 'promo',
                'object_id' => 539]); // 1406752162
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5006, 'organization_id' => 2, 'object_type' => 'promo',
                'object_id' => 585]); // 1410276639
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5006, 'organization_id' => 2, 'object_type' => 'promo',
                'object_id' => 733]); // 1434642460
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5007, 'organization_id' => 2, 'object_type' => 'promo',
                'object_id' => 745]); // 1437158210
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5007, 'organization_id' => 2, 'object_type' => 'promo',
                'object_id' => 747]); // 1437402113
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5008, 'organization_id' => 2, 'object_type' => 'promo',
                'object_id' => 801]); // 1444146540
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5008, 'organization_id' => 2, 'object_type' => 'promo',
                'object_id' => 823]); // 1447105961
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5009, 'organization_id' => 2, 'object_type' => 'promo',
                'object_id' => 829]); // 1448045421
        DB::table('portal_object_ownership')
            ->insert(['user_id' => 5009, 'organization_id' => 2, 'object_type' => 'promo',
                'object_id' => 994]); // abcd
        //endregion

        //endregion

        //region Operator Level Merchants
        //region Merchants
        DB::table('portal_operator_merchant_map')
            ->insert(['user_id'=>5003, 'merchant_id'=>1083]);
        DB::table('portal_operator_merchant_map')
            ->insert(['user_id'=>5003, 'merchant_id'=>1095]);
        DB::table('portal_operator_merchant_map')
            ->insert(['user_id'=>5003, 'merchant_id'=>1112]);
        DB::table('portal_operator_merchant_map')
            ->insert(['user_id'=>5003, 'merchant_id'=>103918]);
        DB::table('portal_operator_merchant_map')
            ->insert(['user_id'=>5003, 'merchant_id'=>104769]);
        //endregion

        //endregion
    }
}
