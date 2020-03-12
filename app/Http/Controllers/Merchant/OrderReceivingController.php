<?php namespace App\Http\Controllers\Merchant;

use \App\Model\Lookup;
use App\Model\Merchant_vivonet_info_Maps;
use \Request;
use \App\Model\MerchantMessageMap;
use \App\Http\Controllers\Controller;
use \App\Service\Utility;
use \DB;

class OrderReceivingController extends Controller {

    //Load Merchant Page
    public function index() {
        $merchant_id = session('current_merchant_id');

        $utility = new Utility();

        //Get Lookup Values
        $message_types = Lookup::where('type_id_field', '=', 'message_type')->get()->toArray();
        $message_formats = Lookup::where('type_id_field', '=', 'order_del_type')->get()->toArray();

        $send_orders = MerchantMessageMap::where('merchant_id', '=', $merchant_id)->get();

        //Decode the Lookup Id's into Readable values for the view table
        foreach ($send_orders as $send_order) {
            //Message Type
            $send_order->message_type_value = $utility->findArrayPropertyValueWithProperty($message_types, 'type_id_value',
                $send_order->message_type, 'type_id_name');
            //Message Format
            $send_order->message_format_value = $utility->findArrayPropertyValueWithProperty($message_formats, 'type_id_value',
                $send_order->message_format, 'type_id_name');
        }
        $send_orders = $send_orders->toArray();

        return ['send_orders' => $send_orders, 'messages_types' => $message_types, 'message_formats' => $message_formats];
    }

    /**
     * Remove an Order Receiving Record
     *
     * @param  int $id
     * @param Merchant_vivonet_info_Maps $vivonet
     * @return int
     */
    public function destroy($id, Merchant_vivonet_info_Maps $vivonet) {
        try {
            $send_order = MerchantMessageMap::find($id);
            if ($send_order->message_format == 'V') {
                $vivonet->deleteWithMerchantID($send_order->merchant_id);
            }
            MerchantMessageMap::destroy($id);
            DB::commit();
            return 1;
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json($exception->getMessage(), 404);
        }
    }
}



