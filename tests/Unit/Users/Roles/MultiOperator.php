<?php

namespace Tests\Unit\Users\Roles;


class MultiOperator extends RoleType
{
    protected $role_name = 'multi_operator';
    protected $permissions = [
        'op_nav',
        'menu_quick_nav',
        'marketing_nav',
        'reports_nav',
        'order_mgmt_nav',
        'operator_settings',
        'op_merch_select',
        'mng_usrs',
        'home_nav',
        'multi_merchs_filter',
        'create_owner_oper',
        'create_store_mngr'
    ];
}