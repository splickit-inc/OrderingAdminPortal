<?php

namespace Tests\Unit\Users\Roles;


class StoreAssociate extends RoleType
{
    protected $role_name = 'store_associate';
    protected $permissions = [
        'order_mgmt_nav'
    ];
}