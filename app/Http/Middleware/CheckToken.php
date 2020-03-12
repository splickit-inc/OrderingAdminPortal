<?php

namespace App\Http\Middleware;

use Closure;
use \Illuminate\Support\Facades\Cookie;

class CheckToken {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $authHeader = $request->header('Authorization');
        if(is_null($authHeader)){
            return response()->json(["errors" => 'Unauthorized', "status" => 401], 401);
        }
        $authHeaderParts = explode(' ', $authHeader);
        if (count($authHeaderParts) != 2) {
            return response()->json(["errors" => 'Invalid auth header', "status" => 400], 400);
        }
        $type = $authHeaderParts[0];
        $token = $authHeaderParts[1];
        if (strtolower($type) != 'bearer' || empty($token)) {
            return response()->json(["errors" => 'Bad token format', "status" => 400], 400);
        }
        $sessionToken = session('token');
        if(is_null($sessionToken)){
            Cookie::queue(Cookie::forget('user_data'));
        }
        if($sessionToken!=$token){
            return response()->json(["errors" => 'Forbidden', "status" => 403], 403);
        }
        return $next($request);
    }
}
