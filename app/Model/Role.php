<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'portal_roles';


    public $role_to_create = [
        [
            'id' => 1,
            'name' => 'Order140 Super User',
            'permission' => 'create_super_user',
            'permissions_table' => [
                'Manage Users',
                'Manage Brand',
                'Manage Accounts',
                'Customer Service',
                'Manage Menu',
                'Menu Quick - Edit',
                'Marketing',
                'Reporting',
                'Creative'
            ]
        ],
        [
            'id' => 2,
            'name' => 'Partner Admin',
            'permission' => 'create_ptnr_admin',
            'permissions_table' => [
                'Manage Users',
                'Manage Brand',
                'Manage Accounts',
                'Manage Menu',
                'Menu Quick - Edit',
                'Marketing',
                'Reporting',
                'Creative'
            ]
        ],
        [
            'id' => 3,
            'name' => 'Reseller Account Manager',
            'permission' => 'create_var_acct_mngr',
            'permissions_table' => [
                'Manage Users',
                'Manage Brand',
                'Manage Accounts',
                'Manage Menu',
                'Menu Quick - Edit',
                'Marketing',
                'Reporting',
                'Creative'
            ]
        ],
        [
            'id' => 5,
            'name' => 'Store Owner Operator',
            'permission' => 'create_owner_oper',
            'permissions_table' => [
                'Manage Users',
                'Manage Accounts',
                'Operator Dashboard',
                'Menu Quick - Edit',
                'Manage Orders',
                'Ordering On/Off',
                'Marketing',
                'Reporting'
            ]
        ],
        [
            'id' => 6,
            'name' => 'Store Manager',
            'permission' => 'create_store_mngr',
            'permissions_table' => [
                'Manage Users',
                'Operator Dashboard',
                'Menu Quick - Edit',
                'Manage Orders',
                'Ordering On/Off',
            ]
        ],
        [
            'id' => 8,
            'name' => 'Brand Manager',
            'permission' => 'create_brand_mngr',
            'permissions_table' => [
                'Manage Users',
                'Manage Brand',
                'Manage Accounts',
                'Customer Service',
                'Manage Menu',
                'Menu Quick - Edit',
                'Marketing',
                'Reporting'
            ]
        ],
        [
            'id' => 7,
            'name' => 'Multi Location Operator',
            'permission' => 'create_multi_loc_op',
            'permissions_table' => [
                'Manage Users',
                'Manage Accounts',
                'Operator Dashboard',
                'Menu Quick - Edit',
                'Manage Orders',
                'Marketing',
                'Reporting'
            ]
        ],
        [
            'id' => 9,
            'name' => 'Store Associate',
            'permission' => 'create_store_assct',
            'permissions_table' => [
                'Manage Orders'
            ]
        ]
    ];


    public function permissions()
    {

        $relation = $this->belongsToMany(Permission::class, 'portal_permissions_roles_map', 'role_id', 'permission_id');

        return $relation;
    }

    public function setPrimaryKey($key)
    {
        $this->primaryKey = $key;
    }


    public function givePermissionTo(Permission $permission)
    {
        return $this->permissions()->save($permission);
    }

    public function getCreateRoles()
    {
        $permissions = session('user_permissions')->toArray();
        $roles = [];

        foreach ($this->role_to_create as $role_to_create) {
            if (in_array($role_to_create['permission'], $permissions)) {
                $roles[] = $role_to_create;
            }
        }
        return $roles;
    }
}
