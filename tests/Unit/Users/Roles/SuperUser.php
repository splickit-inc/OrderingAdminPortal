<?php

namespace Tests\Unit\Users\Roles;


use App\User;

class SuperUser extends RoleType
{
    protected $role_name = 'super_user';
    protected $permissions = [
        'accounts_nav',
        'brand_nav',
        'brands_filter',
        'create_brand_mngr',
        'create_multi_loc_op',
        'create_owner_oper',
        'create_ptnr_admin',
        'create_store_assct',
        'create_store_mngr',
        'create_super_user',
        'create_usr_org_select',
        'create_var_acct_mngr',
        'cs_nav',
        'cst_srv_usr_edit',
        'customer_service_users',
        'home_nav',
        'login_as_user',
        'marketing_nav',
        'menu_full_nav',
        'mng_usrs',
        'mng_vars',
        'multi_merchs_filter',
        'nutrition',
        'onload_accounts_list',
        'onload_menu_list',
        'reports_nav',
        'show_live_orders',
        'sites_nav',
        'pk_lot_users'
    ];
}