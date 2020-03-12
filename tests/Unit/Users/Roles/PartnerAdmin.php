<?php

namespace Tests\Unit\Users\Roles;


class PartnerAdmin extends RoleType
{
    protected $role_name = 'partner_admin';
    protected $permissions = [
        'accounts_nav',
        'menu_full_nav',
        'marketing_nav',
        'sites_nav',
        'reports_nav',
        'brand_nav',
        'mng_usrs',
        'home_nav',
        'brands_filter',
        'multi_merchs_filter',
        'create_var_acct_mngr',
        'create_owner_oper',
        'create_store_mngr',
        'create_brand_mngr',
        'show_live_orders',
        'onload_menu_list',
        'onload_accounts_list',
        'create_multi_loc_op',
        'create_store_assct',
        'nutrition'
    ];
}