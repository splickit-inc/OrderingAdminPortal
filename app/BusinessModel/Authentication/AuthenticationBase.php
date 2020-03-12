<?php
namespace App\BusinessModel\Authentication;

use Illuminate\Support\Facades\App;

/**
 * Here is all the general functionality related to Authentication
 */
class AuthenticationBase {
    protected $baseSessionState = [
        'permissions' => [],
        'visibility' => ""
    ];

    protected function getVisibilityInstance($visibility)
    {
        return App::make('users.' . $visibility);
    }
}