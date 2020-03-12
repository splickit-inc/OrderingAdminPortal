<?php

namespace App\BusinessModel\Visibility\Users;

use App\Model\Merchant;
use App\Model\MerchantMenuMap;
use App\Model\Skin;
use App\Model\UserMerchantMap;
use App\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class Operator extends Visibility
{

    public function setSessionState($user)
    {
        try {
            Log::info('Trying Operator Set Session State');
            $merchant_count = UserMerchantMap::where('user_id', '=', $user->id)->count();
            session(['operator_merchant_count' => $merchant_count]);
            $merchant_map = UserMerchantMap::where('user_id', '=', $user->id)->first();
            if (empty($merchant_map)) {
                Log::info('Error, No Operator Set for User with ID : '.$user->id);
                throw new \Exception("There isn't any merchant related with this user. Please complete contact support in order to complete your configuration.");
            }
            $merchant = Merchant::find($merchant_map->merchant_id);
            Log::info('Operator Merchant '.json_encode($merchant));

            $menu_count = MerchantMenuMap::where('merchant_id', '=', $merchant->merchant_id)->count();

            if ($menu_count > 0) {
                $menu = MerchantMenuMap::where('merchant_id', '=', $merchant->merchant_id)->first()->toArray();
            } else {
                $menu = ['menu_id' => false];
            }

            $web_skin = Skin::where('brand_id', '=', $merchant->brand_id)->first();
            if (!empty($web_skin)) {
                session(['current_web_skin_id' => $web_skin->skin_id]);
            }
            //   $merchant_ids = array_column($merchants, 'merchant_id');
            //  session(['user_merchant_ids' => $merchant['merchant_id']]);
            session(['current_merchant_id' => $merchant->merchant_id]);
            session(['operator_merchant_id' => $merchant->merchant_id]);
            session(['current_menu_id' => $menu['menu_id']]);
            session(['current_brand_id' => $merchant->brand_id]);
            session(['operator_merchant' => $merchant]);
            return ['operator_merchant_count' => $merchant_count, 'menu_id' => $menu['menu_id']];
        } catch (\Exception $exception) {
            Log::error('Exception on Operator SetSessionState: '.json_encode($exception));
            throw $exception;
        }
    }

    public function getSessionState()
    {
        Log::info('Trying Operator Get getSessionState');

        return [
            'operator_merchant_count' => session('operator_merchant_count'),
            'operator_merchant' => session('operator_merchant'),
            'user_related_data' => [
                'current_merchant_id' => session('current_merchant_id'),
                'operator_merchant_id' => session('operator_merchant_id'),
                'current_menu_id' => session('current_menu_id'),
                'current_brand_id' => session('current_brand_id'),
                'brand_id' => session('current_brand_id'),
                'current_web_skin_id' => session('current_web_skin_id'),

            ]
        ];
    }

    public function getAllUsers()
    {
        /** @var User $user */
        $user = App::make(User::class);
        //user the user_id
        $result = $user->filterRoleHierarchy(
            $user->getOperatorRelatedUsers(session('user_id')),
            session('user_roles')[0]
        )->distinct('email')->get()->toArray();
        return ['users' => $result];
    }
}