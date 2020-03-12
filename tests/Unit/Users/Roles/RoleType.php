<?php

namespace Tests\Unit\Users\Roles;


use App\Model\Role;
use App\User;
use Illuminate\Support\Facades\Log;
use Tests\Unit\Visibility\IUserVisibility;

abstract class RoleType implements IRoleType
{
    /**
     * @var string $role_name This is the name of the role to configure the factory in order to obtain the correct record from the database see factories for roles.
     */
    protected $role_name;

    /**
     * @var array $permissions this are the permissions response that we expect to receive in the response for each role.
     */
    protected $permissions;

    /**
     * @var IUserVisibility $visibility ;
     */
    protected $visibility;

    /**
     * Set if you need the visibility configuration enabled.
     * @var bool
     */
    protected $visibilityConfiguration = true;


    protected $basicLoginAttemptResponseStructure = [
        'status',
        'token',
        'permissions',
        'user' => [
            'id',
            'visibility'
        ]
    ];

    protected $basicLoginAttemptResponseData = [
        'status' => 1,
        'user' => [
            'id' => '',
            'email' => '',
            'first_name' => '',
            'visibility' => ''
        ]
    ];

    protected $basicSessionInfoResponseStructure = [
        'permissions',
        'visibility'
    ];

    public function __construct(IUserVisibility $visibility)
    {
        $this->visibility = $visibility;
    }

    /**
     * @param int $organization_id
     * @return User
     */
    function getUser($organization_id = 1)
    {
        $user = factory(User::class)->create(['visibility' => $this->visibility->getVisibilityName(), 'organization_id' => $organization_id]);
        if ($this->visibilityConfiguration == true) {
            $user = $this->visibility->configureUserForCurrentVisibility($user);
        }
        /** @var Role $role */
        $role = factory(Role::class, $this->role_name)->make();
        $user->roles()->attach($role->id);
        return $user;
    }

    /**
     * @param array $responseData
     * @return bool
     */
    function checkPermissionsFromResponse(array $responseData)
    {
        if (!empty($responseData['permissions'])) {
            $not_implemented_permissions = array_diff($responseData['permissions'], $this->permissions);
            Log::debug("This is the difference between configured permissions and the current response");
            Log::debug($not_implemented_permissions);
            return empty($not_implemented_permissions);
        } else {
            return false;
        }
    }

    function getLoginAttemptResponseStructure()
    {
        if ($this->visibilityConfiguration == true) {
            return array_merge($this->basicLoginAttemptResponseStructure, $this->visibility->getLoginAttemptResponseSpecificStructure());
        }
        return $this->visibility->getLoginAttemptResponseSpecificStructure();
    }

    function getLoginAttemptResponseData(User $user)
    {
        if ($this->visibilityConfiguration == true) {
            $response = $this->basicLoginAttemptResponseData;
            $response['user'] = [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'visibility' => $this->visibility->getVisibilityName()
            ];
            $response = array_merge($response, $this->visibility->getLoginAttemptResponseSpecificData($user));
            return $response;
        }
        return $this->visibility->getLoginAttemptResponseSpecificData($user);
    }

    function getSessionInfoResponseStructure()
    {
        return array_merge($this->basicSessionInfoResponseStructure, $this->visibility->getSessionInfoResponseSpecificStructure());
    }

    function getSessionInfoResponseData(User $user)
    {
        $response = $this->visibility->getLoginAttemptResponseSpecificData($user);
        $response['visibility'] = $this->visibility->getVisibilityName();
        return $response;
    }

    /**
     * Set the visibility configuration enabled or disabled.
     * @param bool $visibilityConfiguration
     */
    function setVisibilityConfiguration($visibilityConfiguration)
    {
        $this->visibilityConfiguration = $visibilityConfiguration;
        $this->visibility->setVisibilityConfiguration($visibilityConfiguration);
    }
}