<?php

namespace Tests\Unit\Visibility;


use App\Model\Merchant;
use App\User;

class Operator extends UserVisibility
{
    protected $name = 'operator';

    function getLoginAttemptResponseSpecificStructure()
    {
        if ($this->visibilityConfiguration == true) {
            $basicStructure = parent::getLoginAttemptResponseSpecificStructure();
            return array_merge($basicStructure, [
                'operator_merchant_count',
                'menu_id',
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
                'operator_merchant_count' => 1
            ]);
        } else {
            return ['errors' => 'There isn\'t any merchant related with this user. Please complete contact support in order to complete your configuration.'];
        }
    }

    function getSessionInfoResponseSpecificStructure()
    {
        $basicStructure = parent::getSessionInfoResponseSpecificStructure();
        return array_merge($basicStructure, [
            'operator_merchant_count',
            'operator_merchant',
            'user_related_data' => [
                'current_merchant_id',
                'operator_merchant_id',
                'current_menu_id',
                'current_brand_id',
                'current_web_skin_id'
            ]
        ]);
    }

    function configureUserForCurrentVisibility(User $user)
    {
        /** @var Merchant $merchant */
        $merchant = factory(Merchant::class, 'MerchantWithRandomBrand')->create();
        $user->merchants()->save($merchant);
        return $user;
    }
}