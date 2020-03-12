<?php

namespace App\Http\Middleware;

use App\BusinessModel\Visibility\VisibilityFactory;
use Closure;
use Illuminate\Http\Request;
use App\BusinessModel\Visibility\ParkingLot;

class ParkingLotVisibilityConfiguration
{
    /**
     * @param Request $request
     * @param Closure $next
     * @param null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        try {
            $visibility = $request->session()->get('user_visibility');
            VisibilityFactory::setVisibility($visibility, ParkingLot\IVisibility::class);
        } catch (\Exception $exception) {
            return response()->json(["errors" => 'Forbidden', "status" => 403], 403);
        }
        return $next($request);
    }
}