<?php

namespace App\BusinessModel\Authentication;


use App\User;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;

/**
 * Here is the related functionality to the user
 */
class Users extends AuthenticationBase
{
    /**
     * Get all the session variables for all kind of users, the user kind will be injected using
     * the value of $user->visibility
     * @return array
     */
    public function getSessionState()
    {
        Log::info('Starting Users getSessionState');

        $result = [];
        if (!$this->isUserAuthenticated()) {
            Log::info('User Is not Authenticated. Returning base session state.');
            return $this->baseSessionState;
        }
        $user = $this->getUserAuthenticated();
        Log::info('Authenticated User: '.json_encode($user));
        $user_visibility = $this->getVisibilityInstance($user->visibility);
        if ($user_visibility != null)
            $result = $user_visibility->getSessionState();
            Log::info('User Visibility Session State Response: '.json_encode($result));

        $permissions = session('user_permissions');
        Log::info('Current Session Permissions: '.json_encode($permissions));

        $result = array_merge($result,
            [
                'permissions' => array_values($permissions->toArray()),
                'visibility' => session('user_visibility')
            ]);

        try {
            $result = array_merge($result, [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'email' => $user->email,
                    'visibility' => $user->visibility
                ]
            ]);
        } catch (\Exception $exception) {
            \Log::error($exception);
        }
        return $result;
    }


    /**
     * Set all the session variables for all kind of users, the user kind will be injected using
     * the value of $user->visibility
     *
     * @param User $user
     * @return array
     * @throws \Exception
     */
    public function setSessionState($user)
    {
        try {
            Log::info('Trying to Get Session State');
            $result = [];
            $user_permissions = $this->getPermissions($user);
            Log::info('user permissions response; '.json_encode($user_permissions));

            session(['user_roles' => array_column($user->roles->toArray(), 'id')]);
            session(['user_permissions' => collect($user_permissions)]);
            session(['user_permission_set' => $user_permissions]);
            session(['user_visibility' => $user->visibility]);
            session(['user_organization_id' => $user->organization_id]);
            session(['user_id' => $user->id]);
            $visibility_instance = $this->getVisibilityInstance($user->visibility);
            if ($visibility_instance != null) {
                $result = $visibility_instance->setSessionState($user);
            }

            $token = str_random(32);
            session(['token' => $token]);
            $result = array_merge(
                $result,
                [
                    "status" => 1, 'token' => $token,
                    'permissions' => $user_permissions,
                    'user' => [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'email' => $user->email,
                        'visibility' => $user->visibility
                    ]
                ]);
            return $result;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Get the list of user's permissions
     * @param User $user
     * @return array
     */
    public function getPermissions($user)
    {
        $user_roles = $user->roles;

        $user_permissions = [];

        foreach ($user_roles as $role) {
            $permissions = $role->permissions->toArray();

            foreach ($permissions as $permission) {
                $user_permissions[] = $permission['code'];
            }
        }
        return array_unique($user_permissions);
    }

    /**
     * Attempt to login the user with the data from the request (email and password)
     * @param array $data
     * @return User
     */
    public function loginAttempt($data)
    {
        if (!Auth::attempt($data)) {
            return null;
        }
        $user = Auth::user();
        return User::find($user->id);
    }

    /**
     * Check if there the request owner is from an authenticated user or if the laravel
     * instance has an authenticated user.
     */
    public function isUserAuthenticated()
    {
        return Auth::check();
    }

    /**
     * Get the current user authenticated if there isn't a user authenticated returns null
     * @return User
     */
    public function getUserAuthenticated()
    {
        return Auth::User();
    }

    /**
     * Get the visibility of the current authenticated user if there is no atuthenticated user
     * returns empty string
     * @return string
     */
    public function getCurrentUserVisibility()
    {
        $user = $this->getUserAuthenticated();
        return $user ? $user->visibility : "";
    }

    public function getAllUsers()
    {
        $user_visibility = $this->getCurrentUserVisibility();
        $visibility_instance = $this->getVisibilityInstance($user_visibility);
        return $visibility_instance->getAllUsers();
    }

}