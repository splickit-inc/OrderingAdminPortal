<?php
/**
 * Created by PhpStorm.
 * User: pablo.daza
 * Date: 3/5/18
 * Time: 3:59 PM
 */

namespace App\BusinessModel\Visibility\Users;

use App\User;
use Illuminate\Support\Facades\Log;

interface IVisibility {
    /**
     * set all the session variables for all type of users
     * @param User $user
     * @return array
     */
    public function setSessionState($user);
    /**
     * get all the session variables for all type of users
     */
    public function getSessionState();

    /**
     * Get all the users for all type of users
     * @return array
     */
    public function getAllUsers();
}