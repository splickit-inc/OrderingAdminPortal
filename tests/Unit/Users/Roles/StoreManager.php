<?php

namespace Tests\Unit\Users\Roles;


class StoreManager extends RoleType
{
    protected $role_name = 'store_manager';
    protected $permissions = [
        'op_nav',
        'menu_quick_nav',
        'order_mgmt_nav',
        'mng_usrs',
        'home_nav',
        'order_on_off',
        'create_store_assct'
    ];
}