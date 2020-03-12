<?php namespace App\Http\Controllers\Operator;

use App\Http\Controllers\SplickitApiCurlController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderManagementController extends SplickitApiCurlController {
    public function currentOrders(Request $request) {
        $this->api_endpoint = 'messages?merchant_id=' . session('current_merchant_id');
        $data = $request->all();

        $open_orders = json_decode($data['open_orders']);

        $visibility = session('user_visibility');

        $order_ids = [];

        if ($visibility != 'operator') {
            Log::info('User is Not Operator. They should not be on this screen.');

            return [
                'past_messages' => [],
                'current_messages' => [],
                'future_messages' => [],
                'late_messages' => [],
                'read_only' => true,
                'valid_operator' => false
            ];
        }

        $response = $this->makeCurlRequest();
        $result = [
            'late_messages' => [],
            'current_messages' => [],
            'future_messages' => [],
            'past_messages' => []
        ];

        foreach ($response['late_messages'] as $index => $message) {
            $array_temp = json_decode($message['portal_order_json'], true);
            unset($message['portal_order_json']);
            $array_temp = array_merge($array_temp, $message);

            if (in_array($message['order_id'], $open_orders)) {
                $array_temp['show_detail'] = true;
            }
            else {
                $array_temp['show_detail'] = false;
            }

            array_push($result['late_messages'], $array_temp);
            $order_ids[] = $message['order_id'];
        }

        foreach ($response['current_messages'] as $index => $message) {
            $array_temp = json_decode($message['portal_order_json'], true);
            unset($message['portal_order_json']);
            $array_temp = array_merge($array_temp, $message);

            if (in_array($message['order_id'], $open_orders)) {
                $array_temp['show_detail'] = true;
            }
            else {
                $array_temp['show_detail'] = false;
            }

            array_push($result['current_messages'], $array_temp);
            $order_ids[] = $message['order_id'];
        }

        foreach ($response['future_messages'] as $index => $message) {
            $array_temp = json_decode($message['portal_order_json'], true);
            unset($message['portal_order_json']);
            $array_temp = array_merge($array_temp, $message);

            if (in_array($message['order_id'], $open_orders)) {
                $array_temp['show_detail'] = true;
            }
            else {
                $array_temp['show_detail'] = false;
            }

            array_push($result['future_messages'], $array_temp);
            $order_ids[] = $message['order_id'];
        }

        foreach ($response['past_messages'] as $index => $message) {
            $array_temp = json_decode($message['portal_order_json'], true);
            unset($message['portal_order_json']);
            $array_temp = array_merge($array_temp, $message);

            if (in_array($message['order_id'], $open_orders)) {
                $array_temp['show_detail'] = true;
            }
            else {
                $array_temp['show_detail'] = false;
            }

            array_push($result['past_messages'], $array_temp);
            $order_ids[] = $message['order_id'];
        }

        $previous_orders = session('current_order_ids');
        $new_order = false;

        if (!is_null($previous_orders)) {
            foreach ($order_ids as $order_id) {
                if (!in_array($order_id, $previous_orders)) {
                    $new_order = true;
                }
            }
        }

        session(['current_order_ids' => $order_ids]);

        return [
            'past_messages' => array_values($result['past_messages']),
            'current_messages' => array_values($result['current_messages']),
            'future_messages' => array_values($result['future_messages']),
            'late_messages' => array_values($result['late_messages']),
            'read_only' => $response['read_only'],
            'valid_operator' => true,
            'new_order' => $new_order
        ];
    }

    public function completeOrder(Request $request) {
        $data = $request->all();

        $this->api_endpoint = 'messages/' . $data['message_id'] . '/markcomplete';

        $this->data = $data;
        $this->merchant_id = session('current_merchant_id');

        //Audit Data
        $this->audit_data['auditable_id'] = $data['message_id'];
        $this->audit_data['action'] = 'Update';
        $this->audit_data['auditable_type'] = 'Order-MessageComplete';

        return $this->makeCurlRequest(true);
    }
}