<?php

namespace Tests\Unit\Users\Roles;


use App\User;

interface IRoleType
{
    function getUser($organization_id = 1);

    function checkPermissionsFromResponse(array $responseData);

    function getLoginAttemptResponseData(User $user);

    function getLoginAttemptResponseStructure();

    function getSessionInfoResponseStructure();

    function getSessionInfoResponseData(User $user);

    function setVisibilityConfiguration($visibilityConfiguration);
}