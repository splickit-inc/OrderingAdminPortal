<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Role;

class Permission extends Model {
    protected $table = 'portal_permissions';

    public function roles() {
        return $this->belongsToMany(Role::class, 'portal_permissions_roles_map');
    }
}
