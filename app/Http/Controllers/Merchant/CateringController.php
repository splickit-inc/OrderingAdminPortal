<?php

namespace App\Http\Controllers\Merchant;


use App\Http\Controllers\Controller;
use App\Model\Merchant;
use App\Service\Utility;
use Illuminate\Http\Request;

class CateringController extends Controller
{
    function getCurrentConfiguration(Merchant $model)
    {
        try {
            $merchant_id = session('current_merchant_id');
            /** @var Merchant $merchant */
            $merchant = $model->with('cateringInfo')->find($merchant_id);
            return response()->json($merchant, 200);
        } catch (\Exception $exception) {
            response()->json(['error' => $exception->getMessage()], 404);
        }
    }

    function updateCateringInfo(Request $request, Merchant $model, Utility $utility)
    {
        try {
            $data = $request->all();
            if (array_key_exists('active', $data)) {
                $data['active'] = $utility->convertBooleanYN($data['active']);
            };
            if (array_key_exists('delivery_active', $data)) {
                $data['delivery_active'] = $utility->convertBooleanYN($data['delivery_active']);
            };
            if (array_key_exists('logical_delete', $data)) {
                $data['logical_delete'] = $utility->convertBooleanYN($data['logical_delete']);
            };
            $merchant_id = session('current_merchant_id');
            /** @var Merchant $merchant */
            $merchant = $model->with('cateringInfo')->find($merchant_id);
            $cateringInfo = $merchant->updateCateringInfo($data);
            return response()->json($cateringInfo, 200);
        } catch (\Exception $exception) {
            response()->json(['error' => $exception->getMessage()], 404);
        }
    }
}