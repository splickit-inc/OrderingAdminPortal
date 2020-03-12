<?php

namespace Tests\Unit\Visibility;


use App\User;
use Illuminate\Support\Facades\Log;


class Brand extends UserVisibility
{
    protected $name = 'brand';

    function configureUserForCurrentVisibility(User $user)
    {
        $brand = factory(\App\Model\Brand::class)->create();
        $user->brand()->save($brand);
        return $user->fresh();
    }

    function getLoginAttemptResponseSpecificStructure()
    {
        if ($this->visibilityConfiguration == true) {
            $basicStructure = parent::getLoginAttemptResponseSpecificStructure();
            return array_merge($basicStructure, [
                'user_related_data'
            ]);
        } else {
            return ['errors'];
        }
    }

    function getLoginAttemptResponseSpecificData(User $user)
    {
        if ($this->visibilityConfiguration == true) {
            $basicData = parent::getLoginAttemptResponseSpecificData($user);
            return array_merge($basicData, [
                'user_related_data' => [
                    'brand_id' => $user->brand()->first()->brand_id
                ]
            ]);
        } else {
            return ['errors' => 'There isn\'t any branch related with this user. Please complete contact support in order to complete your configuration.'];
        }
    }

    function getSessionInfoResponseSpecificStructure()
    {
        if ($this->visibilityConfiguration == true) {
            $basicStructure = parent::getSessionInfoResponseSpecificStructure();
            return array_merge($basicStructure, [
                'user_related_data' => [
                    'brand_id'
                ]
            ]);
        } else {
            return ['errors'];
        }
    }
}