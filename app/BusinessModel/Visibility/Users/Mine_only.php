<?php

namespace App\BusinessModel\Visibility\Users;

use App\User;
use Illuminate\Support\Facades\App;

class Mine_only extends Visibility
{
    /**
     * @param User $user
     * @return array
     */
    public function setSessionState($user)
    {
        Log::info('Mine Only setSessionState');
        $user->unprocessed_child_user_ids = [$user->id];
        $children = $user->getAllChildren();
        Log::info('Mine Only Children: '.json_encode($children));
        session(['user_child_users' => $children]);
        return [
            'user_child_users' => $children
        ];
    }

    public function getSessionState()
    {
        Log::info('Trying MineOnly getSessionState');

        return [
            'user_related_data' => [
                'user_child_users' => session('user_child_users')
            ]
        ];
    }

    public function getAllUsers()
    {
        /** @var User $user */
        $user = App::make(User::class);
        //User user_id
        $result = $user->filterRoleHierarchy(
            $user->getMineOnlyUsers(session('user_id')),
            session('user_roles')[0]
        )->distinct('email')->get()->toArray();
        return ['users' => $result];
    }
}