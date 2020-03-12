<?php

namespace App;

use App\Model\Brand;
use App\Model\Merchant;
use App\Model\Organization;
use App\Model\Permission;

use App\Model\PortalBrandManagerBrandMap;
use App\Model\UserMerchantMap;
use App\Notifications\MailResetPasswordNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Model\Role;
use \DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'portal_users';

    public $child_user_ids = [];
    public $unprocessed_child_user_ids = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'visibility', 'logical_delete'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function organization()
    {
        return $this->hasOne(Organization::class, 'id', 'organization_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'portal_role_user_map');
    }

    public function merchants()
    {
        return $this->belongsToMany(Merchant::class, 'portal_operator_merchant_map');
    }

    public function brand()
    {
        return $this->belongsToMany(Brand::class, 'portal_brand_manager_brand_map');
    }

    public function role()
    {
        return $this->hasOne(Role::class);
    }

    public function hasRole($role)
    {

        if (is_string($role)) {
            $this->roles->contains('name', $role);
        }
        return !!$role->intersect($this->roles)->count();
    }

    public function getPermissions()
    {
        return Permission::with('roles')->get();
    }

    public function parent()
    {
        return $this->belongsToMany(User::class, 'portal_user_parent_child', 'user_id', 'parent_id');
    }

    public function children()
    {
        return $this->belongsToMany(User::class, 'portal_user_parent_child', 'parent_id', 'user_id')
            ->join('portal_role_user_map', 'portal_role_user_map.user_id', '=', 'portal_user_parent_child.id')
            ->join('portal_roles', 'portal_roles.id', '=', 'portal_role_user_map.role_id')
            ->select(['portal_users.*', 'portal_roles.name AS role_name', 'portal_roles.description AS role_description'])->distinct();
    }

    public function directChildren($user_id)
    {
        $response = DB::table('portal_user_parent_child')->where('parent_id', '=', $user_id)->get(['user_id'])->toArray();
        return array_column($response, 'user_id');
    }

    public function getAllChildren()
    {

        while (sizeof($this->unprocessed_child_user_ids) != 0) {

            $next_child_id = $this->unprocessed_child_user_ids[0];

            $children_user_ids = $this->directChildren($next_child_id);

            $this->child_user_ids[] = $next_child_id;

            array_splice($this->unprocessed_child_user_ids, 0, 1);

            foreach ($children_user_ids as $found_child_id) {
                if (!in_array($found_child_id, $this->child_user_ids) && !in_array($found_child_id, $this->unprocessed_child_user_ids)) {
                    $this->unprocessed_child_user_ids[] = $found_child_id;
                }
            }
        }
        return $this->child_user_ids;
    }

    public function getWithRoles($user_query)
    {
        return $user_query
            ->join('portal_role_user_map', 'portal_role_user_map.user_id', '=', 'portal_users.id')
            ->join('portal_roles', 'portal_roles.id', '=', 'portal_role_user_map.role_id')
            ->select(['portal_users.*', 'portal_roles.name AS role_name', 'portal_roles.description AS role_description', 'portal_roles.id AS role_id', DB::raw("CONCAT(`first_name`,' ',`last_name`) AS full_name")]);
    }

    public function getGlobalUsers()
    {
        $users = $this->getWithRoles(User::
        orderBy('last_name')
            ->orderBy('first_name'));
        return $users;
    }

    public function getAllUsers($organization_id)
    {
        $users = $this->getGlobalUsers()
            ->where('organization_id', '=', $organization_id);
        return $users;
    }

    public function getMineOnlyUsers($user_id)
    {
        return User::find($user_id)->children();
    }

    public function getBrandRelatedUsers($brand_id)
    {
        //get all the users that has a relation with merchants from this brandID
        $users_related_with_brand = $this->getWithRoles(PortalBrandManagerBrandMap::where
        ('portal_brand_manager_brand_map.brand_id', '=', $brand_id)
            ->join('Merchant', 'portal_brand_manager_brand_map.brand_id', '=',
                'Merchant.brand_id')
            ->join('portal_operator_merchant_map', 'Merchant.merchant_id', '=', 'portal_operator_merchant_map.merchant_id')
            ->join('portal_users', 'portal_users.id', '=', 'portal_operator_merchant_map.user_id')
        );

        //get all the users that is brand manager
        $users_assign_with__brand = $this->getWithRoles(PortalBrandManagerBrandMap::where
        ('brand_id', '=', $brand_id)
            ->join('portal_users', 'portal_users.id', '=', 'user_id'));
        //return the both results without duplicates

        return $users_assign_with__brand->union($users_related_with_brand)->distinct();
    }

    public function getOperatorRelatedUsers($user_id)
    {
        $user_related_merchant_id = UserMerchantMap::where('user_id', '=', $user_id)
            ->first();
        $user_related_merchant_id = $user_related_merchant_id ?
            $user_related_merchant_id->merchant_id : null;

        $users_related_merchant_id = $this->getWithRoles(UserMerchantMap::where('merchant_id', '=', $user_related_merchant_id)
            ->join('portal_users', 'portal_users.id', 'portal_operator_merchant_map.user_id'));

        return $users_related_merchant_id;
    }

    /**
     * Filter and return the query where the role_id field is grater than the current role_id
     *
     * @param Builder $query
     * @param integer $role_id
     * @return Builder
     */
    public function filterRoleHierarchy($query, $role_id)
    {
        return $query
            ->join('portal_roles_hierarchy', 'role_id', '=', 'portal_roles_hierarchy.child_role_id')
            ->where('portal_roles_hierarchy.parent_role_id', '=', $role_id);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MailResetPasswordNotification($token));
    }

    public function checkPermission($permission)
    {
        if (is_object(session('user_permissions'))) {
            if (in_array($permission, session('user_permissions')->all())) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
