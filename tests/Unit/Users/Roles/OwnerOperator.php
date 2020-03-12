<?php

namespace Tests\Unit\Users\Roles;


class OwnerOperator extends RoleType
{
    protected $role_name = 'owner_operator';
    protected $permissions = [
        'op_nav',
        'menu_quick_nav',
        'marketing_nav',
        'reports_nav',
        'order_mgmt_nav',
        'operator_settings',
        'mng_usrs',
        'home_nav',
        'order_on_off',
        'create_store_mngr',
        'create_store_assct',
    ];
}