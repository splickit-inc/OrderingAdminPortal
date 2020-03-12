<?php

namespace Tests\Unit\Users\Roles;


class BrandManager extends RoleType
{
    protected $role_name = 'brand_manager';
    protected $permissions = [
        'accounts_nav',
        'cs_nav',
        'menu_full_nav',
        'marketing_nav',
        'reports_nav',
        'brand_direct_nav',
        'mng_usrs',
        'home_nav',
        'multi_merchs_filter',
        'create_owner_oper',
        'create_store_mngr',
        'onload_menu_list',
        'onload_accounts_list',
        'create_multi_loc_op',
        'create_store_assct',
        'customer_service_users',
        'nutrition',
        'show_live_orders',
        'set_default_loyalty',
        'pk_lot_users'
    ];
}