<?php

namespace App\BusinessModel\Visibility\Users;

use App\Model\PortalBrandManagerBrandMap;
use App\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class Brand extends Visibility
{

    public function setSessionState($user)
    {
        try {
            Log::info('Trying to set session state for brand.');

            $brand_map = PortalBrandManagerBrandMap::where('user_id', '=', $user->id)->first();
            if (!empty($brand_map)) {
                Log::info('Setting Brand Manager Brand for brand_id '.$brand_map->brand_id);
                session(['brand_manager_brand' => $brand_map->brand_id]);
                return [
                    'user_related_data' => [
                        'brand_id' => $brand_map->brand_id
                    ]
                ];
            } else {
                Log::info('No Brand Found for User.');
                throw new \Exception("There isn't any brand related with this user. Please complete contact support in order to complete your configuration.");
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function getSessionState()
    {
        Log::info('Trying Brand getSessionState');

        return [
            'user_related_data' => [
                'brand_id' => session('brand_manager_brand')
            ]
        ];
    }

    public function getAllUsers()
    {
        /** @var User $user */
        $user = App::make(User::class);
        //use the brand_id
        $result = $user->filterRoleHierarchy(
            $user->getBrandRelatedUsers(session('brand_manager_brand')),
            session('user_roles')[0]
        )->distinct('email')->get()->toArray();
        return ['users' => $result];
    }
}