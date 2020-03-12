<?php namespace App\Http\Controllers\Merchant;

use \App\Http\Controllers\Controller;
use App\Model\DeviceCallInHistory;
use \DB;
use \App\Model\Merchant;
use \App\Service\Utility;
use Illuminate\Http\Request;

class OperatorActionsController extends Controller {

    public function index() {
        $merchant_id = session('operator_merchant_id');

        //Get Merchant Data
        $merchant = Merchant::find($merchant_id);
        $merchant = $merchant->toArray();

        $device_call_fail = DeviceCallInHistory::where('merchant_id', '=', session('operator_merchant_id'))->where('auto_turned_off', '=', 1)->first();

        if (is_null($device_call_fail)) {
            $device_call_fail = ['status'=>false];
        }
        else {

            $device_call_fail = ['status'=>true,
                                 'last_call'=> date("F j, Y, g:i a", $device_call_fail->last_call_in_as_integer)
            ];
        }

        $utility = new Utility();

        return ['delivery'=> $utility->convertYNBoolean($merchant['delivery']),
                'ordering_on'=> $utility->convertYNBoolean($merchant['ordering_on']),
                'device_call_fail'=>$device_call_fail];
    }

    public function ordering(Request $request) {
        $data = $request->all();
        $utility = new Utility();

        $merchant_id = session('operator_merchant_id');

        //Get Merchant Data
        $merchant = Merchant::find($merchant_id);

        $merchant->ordering_on = $utility->convertBooleanYN($data['ordering']);
        $merchant->save();
        return 'success';
    }

    public function delivery(Request $request) {
        $data = $request->all();
        $utility = new Utility();

        $merchant_id = session('operator_merchant_id');

        //Get Merchant Data
        $merchant = Merchant::find($merchant_id);

        $merchant->delivery = $utility->convertBooleanYN($data['delivery']);
        $merchant->save();
        return 'success';
    }
}



