<?php

namespace App\BusinessModel\Visibility\Users;


class Visibility implements IVisibility
{


    public function getSessionState()
    {
        return [];
    }

    public function setSessionState($user)
    {
        return [];
    }

    public function getAllUsers()
    {
        return ['users' => []];
    }
}