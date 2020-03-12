<?php

namespace Tests\Unit\Visibility;


use App\User;
use Illuminate\Support\Facades\Log;

abstract class UserVisibility implements IUserVisibility
{
    /**
     * Used to know the selected visibility could be used to inject the visibility automatically.
     * @var $name
     */
    protected $name;

    protected $basicLoginAttemptResponseStructure = [];

    protected $basicLoginAttemptResponseData = [];

    protected $basicSessionInfoResponseStructure = [];

    protected $visibilityConfiguration = true;

    function getVisibilityName()
    {
        return $this->name;
    }

    function getLoginAttemptResponseSpecificData(User $user)
    {
        return $this->basicLoginAttemptResponseData;
    }

    function getLoginAttemptResponseSpecificStructure()
    {
        return $this->basicLoginAttemptResponseStructure;
    }

    function getSessionInfoResponseSpecificStructure()
    {
        return $this->basicSessionInfoResponseStructure;
    }

    function setVisibilityConfiguration($visibilityConfiguration)
    {
        $this->visibilityConfiguration = $visibilityConfiguration;
    }
}