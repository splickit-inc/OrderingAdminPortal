<?php namespace App\Http\Controllers\Merchant;

use App\Model\DeviceCallInHistory;
use App\Model\LeadTimeMap;
use \App\Model\Lookup;
use \App\Http\Controllers\Controller;
use App\Model\Merchant_vivonet_info_Maps;
use \App\Model\MerchantPreptimeInfo;
use App\Model\Payment;
use \App\Model\MerchantMessageMap;
use \App\Model\MerchantTaskRetailInfoMaps;
use \App\Model\MerchantBrinkInfoMaps;
use \App\Model\MerchantFoundryInfoMaps;
use \App\Model\MerchantAdvancedOrderingInfo;
use \App\Model\MerchantMenuMap;
use App\Model\SkinMerchantMap;
use \App\Service\Utility;
use \App\Model\Merchant;
use \Exception;
use App\Model\BillingEntities;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Model\PortalLookupHierarchy;

class OrderingController extends Controller
{

    //Load Merchant Page
    public function index(Merchant $merchant, Merchant_vivonet_info_Maps $vivonet, DeviceCallInHistory $callInHistory)
    {
        $merchant_id = session('current_merchant_id');
        $current_merchant = $merchant->getMerchantWithMenus($merchant_id);
        //Ordering
        try {
            $prep_time = MerchantPreptimeInfo::where(['merchant_id' => $merchant_id])->firstOrFail();
            $prep_time = $prep_time->toArray();
        } catch (Exception $ex) {
            $prep_time = ['entree_preptime_seconds' => null, 'concurrently_able_to_make' => null];
        }

        $payments = Payment::with(['billingEntity'])
            ->with(['paymentType'])
            ->where('merchant_id', '=', $merchant_id)
            ->get()
            ->toArray();

        //Billing Entities Lookup Values
        $billing_entities = BillingEntities::where('brand_id', '=', session('current_brand_id'))
            ->orderBy('name')
            ->get(['name', 'id'])
            ->toArray();

        //PaymentTypes
        $portal_organization_lookup = new PortalLookupHierarchy();
        $payment_types = $portal_organization_lookup->getLookupByOrganizationPermissions(session('user_organization_id'), 'Splickit_Accepted_Payment_Types', session('current_brand_id'));

        //Order Settings
        $order_settings = Merchant::where('merchant_id', '=', $merchant_id)->first(['advanced_ordering', 'tip_minimum_percentage', 'tip_minimum_trigger_amount', 'group_ordering_on']);
        $order_settings = $order_settings->toArray();

        $order_settings['tip_minimum_trigger_amount'] = number_format($order_settings['tip_minimum_trigger_amount'], 2);

        try {
            $advanced_ordering_info = MerchantAdvancedOrderingInfo::where('merchant_id', '=', $merchant_id)->firstOrFail();

            $order_settings['number_of_days_advance'] = $advanced_ordering_info->max_days_out;
        } catch (Exception $e) {
            $order_settings['number_of_days_advance'] = "";
        }

        $utility = new Utility();

        //Get Lookup Values
        $message_types = Lookup::where('type_id_field', '=', 'message_type')->get()->toArray();
//        $message_formats = Lookup::where('type_id_field', '=', 'order_del_type')->get()->toArray();
        $message_formats = $portal_organization_lookup->getLookupByOrganizationPermissions(session('user_organization_id'), 'order_del_type', session('current_brand_id'));

        $send_orders = MerchantMessageMap::where('merchant_id', '=', $merchant_id)->get();

        //Decode the Lookup Id's into Readable values for the view table
        foreach ($send_orders as $send_order) {
            //Message Type
            $send_order->message_type_value = $utility->findArrayPropertyValueWithProperty($message_types, 'type_id_value',
                $send_order->message_type, 'type_id_name');
            //Message Format
            if ($send_order->logical_delete == 'Y') {
                $send_order->logical_delete = false;
            }
            else {
                $send_order->logical_delete = true;
            }

            $send_order->message_format_value = $utility->findArrayPropertyValueWithProperty($message_formats, 'type_id_value',
                $send_order->message_format, 'type_id_name');
            if ($send_order->message_format == 'V') {
                $vivonet = $vivonet->getMerchantVivonet($merchant_id);
                $send_order->vivonet = $vivonet->toArray();
            }
        }
        $send_orders = $send_orders->toArray();

        $menu = $merchant->find($merchant_id)->menu->toArray();

        $dine_in_option = false;
        $allows_curbside_pickup = false;
        $dine_in_option_show = false;

        foreach ($menu as $single_menu) {
            if ($single_menu['pivot']['merchant_menu_type'] == 'pickup') {
                $dine_in_option_show = true;
                if ($single_menu['pivot']['allows_dine_in_orders'] == 1) {
                    $dine_in_option = true;
                }
                if ($single_menu['pivot']['allows_curbside_pickup'] == 1) {
                    $allows_curbside_pickup = true;
                }
            }
        }
        $order_settings['dine_in_option'] = $dine_in_option;
        $order_settings['dine_in_option_show'] = $dine_in_option_show;
        $order_settings['allows_curbside_pickup'] = $allows_curbside_pickup;

        $current_brand = $current_merchant->brand_id;

        $call_in_history_record = null;

        try {
            date_default_timezone_set($current_merchant->time_zone_string);
            $call_in_history_record = $callInHistory->getFieldByMerchant($merchant_id);
            $unix_time = (int) $call_in_history_record->last_call_in_as_integer;
            $call_in_date_time = new \stdClass();
            $call_in_date_time->readable_format = date('M d Y, h:ia', $unix_time);
            $call_in_date_time->last_call_in_date = date('Y-m-d H:i:s', $unix_time);

        } catch (\Exception $exception) {
            $call_in_date_time = null;
            \Log::error($exception->getMessage());
        }

        return [
            "merchant_id" => $merchant_id,
            "brand_id" => $current_brand,
            "prep_time" => $prep_time,
            "payment_groups" => $payments,
            "billing_entities" => $billing_entities,
            "payment_types" => $payment_types,
            'send_orders' => $send_orders,
            'messages_types' => $message_types,
            'message_formats' => $message_formats,
            'order_settings' => $order_settings,
            'menu' => $menu,
            'call_in_history' => $call_in_date_time
        ];
    }

    //Update Prep Time
    public function updatePrepTime(Request $request)
    {
        $data = $request->all();

        $merchant_id = session('current_merchant_id');

        $lead_time = MerchantPreptimeInfo::firstOrNew(['merchant_id' => $merchant_id]);

        $lead_time->entree_preptime_seconds = $data['entree_preptime_seconds'];
        $lead_time->concurrently_able_to_make = $data['concurrently_able_to_make'];

        $lead_time->save();

        return 1;
    }

    //Update Prep Time
    public function updateOrderSettings(Request $request)
    {
        $data = $request->all();

        $merchant_id = session('current_merchant_id');

        $utility = new Utility();

        $merchant = Merchant::find($merchant_id);

        $merchant->advanced_ordering = $utility->convertBooleanYN($data['advanced_ordering']);

        $merchant->tip_minimum_percentage = $data['tip_minimum_percentage'];

        $merchant->tip_minimum_trigger_amount = $data['tip_minimum_trigger_amount'];

        $merchant->group_ordering_on = $data['group_ordering_on'];

        $merchant->save();

        ///Max Days Out
        if ($merchant->advanced_ordering == 'Y') {
            $advanced_ordering_info = MerchantAdvancedOrderingInfo::firstOrNew(['merchant_id' => $merchant_id]);

            $advanced_ordering_info->max_days_out = $data['number_of_days_advance'];
            $advanced_ordering_info->catering_minimum_lead_time = 0;

            $advanced_ordering_info->save();
        }


        if ($data['dine_in_option']) {
            $dine_in_option = 1;
        }
        else {
            $dine_in_option = 0;
        }

        if ($data['allows_curbside_pickup']) {
            $allows_curbside_pickup = 1;
        }
        else {
            $allows_curbside_pickup = 0;
        }

        $merchant_menu_map = MerchantMenuMap::where('merchant_id', '=', $merchant_id)->where('merchant_menu_type', '=', 'pickup')->first();

        if (isset($merchant_menu_map)) {
            $merchant_menu_map->allows_dine_in_orders = $dine_in_option;

            $merchant_menu_map->save();
        }

        if (isset($allows_curbside_pickup)) {
            $merchant_menu_map->allows_curbside_pickup = $allows_curbside_pickup;

            $merchant_menu_map->save();
        }
        return 1;
    }

    //Create Payment Group
    public function createPaymentGroup(Request $request, Payment $payment)
    {
        try {
            $data = $request->all();
            //"payment" => 3877
            $merchant_id = session('current_merchant_id');

            $payment->splickit_accepted_payment_type_id = $data['payment'];

            if ($data['payment'] == 2000) {
                $payment->billing_entity_id = $data['billing_entity'];
            } else {
                $payment->billing_entity_id = null;
            }

            $payment->merchant_id = $merchant_id;
            $payment->save();

            ///Get the New Payment with the Billing Entity and Payment Type Names
            $payment_with_type_billing_entity = Payment::with(['billingEntity'])
                ->with(['paymentType'])
                ->find($payment->id)
                ->toArray();

            return ['new_payment_group' => $payment_with_type_billing_entity];
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage());
        }
    }

    //Create a new Send Order
    public function createSendOrder(Request $request, Merchant_vivonet_info_Maps $vivonet)
    {
        $merchant_id = session('current_merchant_id');

        $data = $request->all();
        DB::beginTransaction();
        try {
            $send_order = new MerchantMessageMap();

            $send_order->merchant_id = $merchant_id;
            $send_order->message_format = $data['message_format'];
            $send_order->delivery_addr = $data['delivery_addr'];
            $send_order->delay = $data['delay'];
            $send_order->message_type = $data['message_type'];

            if (isset($data['info'])) {
                $send_order->info = $data['info'];
            }

            if (isset($data['message_text'])) {
                $send_order->message_text = $data['message_text'];
            }

            if ($send_order->message_format == 'V') {
                $check = $vivonet->getMerchantVivonet($merchant_id);
                $data['vivonet']['merchant_id'] = $merchant_id;
                $vivonetData = $data['vivonet'];

                if ($check) {
                    $send_order = $send_order->getCurrentMessageForVivonet($merchant_id);
                    $send_order->merchant_id = $merchant_id;
                    $send_order->message_format = $data['message_format'];
                    $send_order->delivery_addr = $data['delivery_addr'];
                    $send_order->delay = $data['delay'];
                    $send_order->message_type = $data['message_type'];

                    if (isset($data['info'])) {
                        $send_order->info = $data['info'];
                    }

                    if (isset($data['message_text'])) {
                        $send_order->message_text = $data['message_text'];
                    }
                    $result = $vivonet->createOrderingConfiguration($vivonetData);
                    $send_order->save();
                } else {
                    $result = $vivonet->createOrderingConfiguration($vivonetData);
                    $send_order->save();
                }

                $send_order->vivonet = $result->toArray();
            } elseif ($send_order->message_format == 'A') {
                $task_retail = $data['task_retail'];

                $send_order = MerchantMessageMap::firstOrNew(['merchant_id' => $merchant_id, 'message_format' => 'A']);

                //Send Order
                $send_order->merchant_id = $merchant_id;
                $send_order->message_format = $data['message_format'];
                $send_order->delivery_addr = $data['delivery_addr'];
                $send_order->delay = $data['delay'];
                $send_order->message_type = $data['message_type'];

                if (isset($data['info'])) {
                    $send_order->info = $data['info'];
                }

                if (isset($data['message_text'])) {
                    $send_order->message_text = $data['message_text'];
                }

                $send_order->save();

                $new_task_retail = MerchantTaskRetailInfoMaps::firstOrNew(['merchant_id' => $merchant_id]);
                $merchant = Merchant::find($merchant_id);

                $new_task_retail->brand_id = $merchant->brand_id;
                $new_task_retail->location = $task_retail['location'];
                $new_task_retail->payment_id = $task_retail['payment_id'];
                $new_task_retail->media_id = $task_retail['media_id'];
                $new_task_retail->task_retail_url = $task_retail['task_retail_url'];
                $new_task_retail->task_retail_auth_url = $task_retail['task_retail_auth_url'];
                $new_task_retail->task_retail_username = $task_retail['task_retail_username'];
                $new_task_retail->task_retail_password = $task_retail['task_retail_password'];

                $new_task_retail->save();
            } elseif ($send_order->message_format == 'B' || $send_order->message_format == 'BO') {
                $brink = $data['brink'];

                $send_order = MerchantMessageMap::firstOrNew(['merchant_id' => $merchant_id, 'message_format' => $send_order->message_format]);

                //Send Order
                $send_order->merchant_id = $merchant_id;
                $send_order->message_format = $data['message_format'];
                $send_order->delivery_addr = $data['delivery_addr'];
                $send_order->delay = $data['delay'];
                $send_order->message_type = $data['message_type'];

                if (isset($data['info'])) {
                    $send_order->info = $data['info'];
                }

                if (isset($data['message_text'])) {
                    $send_order->message_text = $data['message_text'];
                }

                $send_order->save();

                $new_brink_order_receiving = MerchantBrinkInfoMaps::firstOrNew(['merchant_id' => $merchant_id]);


                $new_brink_order_receiving->brink_location_token = $brink['brink_location_token'];
                $new_brink_order_receiving->save();
            }
            elseif ($send_order->message_format == 'U') {
                $foundry_pos_record = MerchantFoundryInfoMaps::firstOrNew(['merchant_id' => $merchant_id]);
                $foundry_pos_record->get_menu = 1;
                $foundry_pos_record->logical_delete = 'N';
                $foundry_pos_record->save();
                $send_order->save();
            }
            else {
                $send_order->save();
            }

            $message_type_name = Lookup::where('type_id_field', '=', 'message_type')
                ->where('type_id_value', '=', $send_order->message_type)
                ->first();
            $send_order->message_type_value = $message_type_name->type_id_name;

            $message_format_name = Lookup::where('type_id_field', '=', 'order_del_type')
                ->where('type_id_value', '=', $send_order->message_format)
                ->first();
            $send_order->message_format_value = $message_format_name->type_id_name;


            DB::commit();
            return $send_order->toArray();
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json($exception->getMessage(), 404);
        }

    }

    //Create a new Send Order
    public function updateSendOrder(Request $request, Merchant_vivonet_info_Maps $vivonet)
    {
        $merchant_id = session('current_merchant_id');
        $utility = new Utility();

        $data = $request->all();

        try {


            $send_order = MerchantMessageMap::find($data['map_id']);
            $send_order->merchant_id = $merchant_id;
            $send_order->message_format = $data['message_format'];
            $send_order->delivery_addr = $data['delivery_addr'];
            $send_order->delay = $data['delay'];
            $send_order->message_type = $data['message_type'];
            $send_order->info = $data['info'];

            if ($data['logical_delete']) {
                $send_order->logical_delete = 'N';
            }
            else {
                $send_order->logical_delete = 'Y';
            }

            $send_order->message_text = $data['message_text'];
            $send_order->save();

            if ($send_order->message_format == 'V') {
                $vivonet_record = $vivonet->createOrderingConfiguration($data['vivonet']);
                $send_order->vivonet = $vivonet_record->toArray();
            }
            DB::commit();
            return $send_order;
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json($exception->getMessage(), 404);
        }
    }

    public function deletePaymentGroup($id)
    {
        Payment::destroy($id);
        return 1;
    }

    public function setSkinToMerchant($merchant_id, Request $request, SkinMerchantMap $skinMerchantMap)
    {
        return $skinMerchantMap->createNewRelation($merchant_id, $request->all());
    }

    /***
     * Get all the Lead Times related to the current merchant
     *
     * @var             $merchant
     * @var LeadTimeMap $leadTimeMap
     * @return Response
     */
    public function getLeadTimeByDay($merchant, LeadTimeMap $leadTimeMap)
    {
        $pickup = $leadTimeMap->getLeadTimesByMerchant($merchant, 'R')->get();
        $delivery = $leadTimeMap->getLeadTimesByMerchant($merchant, 'D')->get();
        $result = ['pickup' => $pickup, 'delivery' => $delivery];
        return response()->json($result, 200);
    }

    /**
     * @param           $merchant_id
     * @param Request $request
     * @var LeadTimeMap $leadTimeMap
     * @return Response
     */
    public function setLeadTimeByDay($merchant_id, Request $request, LeadTimeMap $leadTimeMap)
    {
        $data = $request->all();
        $valid = $leadTimeMap->validateLeadTime($merchant_id, $data);
        if (!$valid) {
            return response()->json(['error' => 'Invalid Lead Time, Check if the lead time is already configured.'], 422);
        }
        $result = $leadTimeMap->setLeadTime($data);

        return response()->json($result, 200);
    }

    /**
     * @param             $merchant_id
     * @param             $lead_time_id
     * @param LeadTimeMap $leadTimeMap
     * @return Response
     */
    public function deleteLeadTimeByDay($merchant_id, $lead_time_id, LeadTimeMap $leadTimeMap)
    {
        $result = $leadTimeMap->deleteLeadTime($lead_time_id);
        return response()->json($result, 200);
    }

    public function getCallInHistory(DeviceCallInHistory $callInHistory)
    {
        try {
            $merchant_id = session('current_merchant_id');
            return response()->json($callInHistory->getFieldByMerchant($merchant_id), 200);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['errors' => $exception->getMessage()], 404);
        }
    }
}
