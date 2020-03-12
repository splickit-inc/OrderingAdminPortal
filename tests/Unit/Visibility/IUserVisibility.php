<?php

namespace Tests\Unit\Visibility;


use App\User;

interface IUserVisibility
{
    /**
     * @param User $user
     * @return User
     */
    function configureUserForCurrentVisibility(User $user);

    /**
     * @return string
     */
    function getVisibilityName();

    function getLoginAttemptResponseSpecificData(User $user);

    function getLoginAttemptResponseSpecificStructure();

    function getSessionInfoResponseSpecificStructure();

    /**
     * Change the responses when the visibility configuration is not set.
     * @param bool $visibilityConfiguration
     */
    function setVisibilityConfiguration($visibilityConfiguration);
}