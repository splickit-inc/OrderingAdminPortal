<?php

namespace App\Http\Controllers\CustomerService;


use App\Http\Controllers\Controller;
use App\BusinessModel\Visibility\ParkingLot\IVisibility;
use App\Http\Middleware\ParkingLotVisibilityConfiguration;
use Illuminate\Http\Request;

class ParkingLotUsersController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware(ParkingLotVisibilityConfiguration::class);
    }

    function getParkingLotUsers(Request $request, IVisibility $visibility)
    {
        try {
            $order_by = $request->order_by ? $request->order_by : 'phone_number';
            $order_direction = $request->exists('order_direction') && $request->order_direction == 'false' ? 'ASC' : 'DES';

            $records = $visibility->searchParkingLotRecords($request->search_text, $order_by, $order_direction)->paginate(20);
            return response()->json($records, 200);
        } catch (\Exception $exception) {
            return response()->json(['errors' => $exception->getMessage()], 404);
        }
    }
}