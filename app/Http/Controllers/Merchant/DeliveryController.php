<?php namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Model\DeliveryZone;
use App\Model\Merchant;
use App\Model\MerchantDeliveryInfo;
use App\Model\UserDeliveryLocationMerchantPriceMaps;
use App\Service\Utility;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{

    //Delivery Info Load
    public function index()
    {
        $merchant_id = session('current_merchant_id');

        //Delivery
        try {
            $delivery_info = MerchantDeliveryInfo::where('merchant_id', '=', $merchant_id)->firstOrFail()->toArray();
        } catch (Exception $ex) {
            $delivery_info = ['minimum_order' => null, 'max_days_out' => null, 'minimum_delivery_time' => null, 'allow_asap_on_delivery' => null,
                'delivery_price_type' => null,
            ];
        }

        //Delivery Zones
        $delivery_zones = DeliveryZone::where('merchant_id', '=', $merchant_id)->get()->toArray();

        foreach ($delivery_zones as $index => $delivery_zone) {
            if (isset($delivery_zone['polygon_coordinates'])) {
                if (strlen($delivery_zone['polygon_coordinates']) > 1) {
                    $delivery_zone['polygon_paths'] = [];
                    $polygon_coordinates_array = preg_split("/,|,\s|\s/", $delivery_zone['polygon_coordinates'],
                        -1, PREG_SPLIT_NO_EMPTY);
                    for ($i = 0; $i < count($polygon_coordinates_array); $i += 2) {
                        $delivery_zones[$index]['polygon_paths'][] = [
                            'lat' => $polygon_coordinates_array[$i],
                            'lng' => $polygon_coordinates_array[$i + 1]
                        ];
                    }
                }
            }
        }

        //Latitude and Longitude Location for Delivery Zones
        $lat_lng_location = Merchant::where('merchant_id', '=', $merchant_id)->first(['lat', 'lng'])->toArray();

        //Lookup Values
        $utility = new Utility();
        $lookups = ['delivery_price_type'];
        $lookup_values = $utility->getLookupValues($lookups);

        return [
            "delivery_info" => $delivery_info,
            "delivery_zones" => $delivery_zones,
            "lookup_values" => $lookup_values,
            "lat_long_location" => $lat_lng_location
        ];
    }

    public function updateDeliveryInfo(Request $request)
    {
        $data = $request->all();

        $utility = new Utility();

        $merchant_id = session('current_merchant_id');

        $delivery_info = MerchantDeliveryInfo::firstOrNew(['merchant_id' => $merchant_id]);

        $delivery_info->minimum_order = 0;

        $delivery_info->max_days_out = $data['max_days_out'];
        $delivery_info->minimum_delivery_time = $data['minimum_delivery_time'];
        $delivery_info->minimum_order = $data['minimum_order'] != null ? $data['minimum_order'] : 0;


        $delivery_info->delivery_increment = $data['delivery_increment'];
        $delivery_info->allow_asap_on_delivery = $utility->convertBooleanYN($data['allow_asap_on_delivery']);

        $delivery_info->save();

        return 1;
    }

    public function createDeliveryZone(Request $request)
    {
        $merchant_id = session('current_merchant_id');

        $data = $request->all();

        $delivery_zone = new DeliveryZone();

        $delivery_zone->merchant_id = $merchant_id;
        $delivery_zone->name = $data['name'];

        if (isset($data['distance_up_to'])) {
            $delivery_zone->distance_up_to = $data['distance_up_to'];
        }

        if (isset($data['zip_codes'])) {
            $delivery_zone->zip_codes = $data['zip_codes'];
        }

        if (isset($data['polygon_coordinates'])) {
            $data['polygon_coordinates'] = array_map(function ($coordinate) {
                return str_replace(',', ' ', $coordinate);
            }, $data['polygon_coordinates']);

            $delivery_zone->polygon_coordinates = implode(",", $data['polygon_coordinates']);
        }


        $delivery_zone->price = $data['price'];
        $delivery_zone->minimum_order_amount = $data['minimum_order_amount'];

        $delivery_zone->delivery_type = $data['delivery_type'];

        $delivery_zone->save();

        $delivery_zone = $delivery_zone->toArray();

        if (isset($data['polygon_coordinates'])) {
            $polygon_coordinates_array = preg_split("/,|,\s|\s/", $delivery_zone['polygon_coordinates'],
                -1, PREG_SPLIT_NO_EMPTY);
            for ($i = 0; $i < count($polygon_coordinates_array); $i += 2) {
                $delivery_zone['polygon_paths'][] = [
                    'lat' => $polygon_coordinates_array[$i],
                    'lng' => $polygon_coordinates_array[$i + 1]
                ];
            }
        }

        $this->flushDeliveryZoneCaching($merchant_id);

        return $delivery_zone;
    }

    public function updateDeliveryZone(Request $request)
    {
        $merchant_id = session('current_merchant_id');

        $data = $request->all();

        $delivery_zone = DeliveryZone::find($data['map_id']);

        $delivery_zone->merchant_id = $merchant_id;
        $delivery_zone->name = $data['name'];

        if (isset($data['distance_up_to'])) {
            $delivery_zone->distance_up_to = $data['distance_up_to'];
        }

        if (isset($data['zip_codes'])) {
            $delivery_zone->zip_codes = $data['zip_codes'];
        }

        if (isset($data['polygon_coordinates'])) {
            if (is_array($data['polygon_coordinates'])) {
                $data['polygon_coordinates'] = array_map(function ($coordinate) {
                    return str_replace(',', ' ', $coordinate);
                }, $data['polygon_coordinates']);
                $delivery_zone->polygon_coordinates = implode(",", $data['polygon_coordinates']);
            }
        }

        $delivery_zone->price = $data['price'];
        $delivery_zone->minimum_order_amount = $data['minimum_order_amount'];

        $delivery_zone->delivery_type = $data['delivery_type'];

        $delivery_zone->save();

        $delivery_zone = $delivery_zone->toArray();

        if (isset($data['polygon_coordinates'])) {
            $polygon_coordinates_array = preg_split("/,|,\s|\s/", $delivery_zone['polygon_coordinates'],
                -1, PREG_SPLIT_NO_EMPTY);
            for ($i = 0; $i < count($polygon_coordinates_array); $i += 2) {
                $delivery_zone['polygon_paths'][] = [
                    'lat' => $polygon_coordinates_array[$i],
                    'lng' => $polygon_coordinates_array[$i + 1]
                ];
            }
        }

        $this->flushDeliveryZoneCaching($merchant_id);

        return $delivery_zone;
    }

    public function updateDeliveryZoneDefinedBy(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();

            $merchant_id = session('current_merchant_id');

            $delivery_info = MerchantDeliveryInfo::firstOrNew(['merchant_id' => $merchant_id]);

            $delivery_info->delivery_price_type = $data['delivery_zone_defined_by'];

            $delivery_info->save();
            DB::commit();
            $this->flushDeliveryZoneCaching($merchant_id);

            DB::beginTransaction();

            DeliveryZone::where('merchant_id', '=', $merchant_id)->where('name','=', 'Door Dash')->delete();

            DB::commit();
            $this->flushDeliveryZoneCaching($merchant_id);

            response()->json('1', 200);
        } catch (\Exception $exception) {
            DB::rollback();
            return response()->json($exception->getMessage(), 500);
        }
    }

    public function flushDeliveryZoneCaching($merchant_id)
    {
        UserDeliveryLocationMerchantPriceMaps::where('merchant_id', '=', $merchant_id)->delete();
    }

    public function deleteDeliveryZone($map_id)
    {
        DeliveryZone::destroy($map_id);
        return 1;
    }

    public function addDoorDash() {
        $merchant_id = session('current_merchant_id');

        $delivery_info = MerchantDeliveryInfo::firstOrNew(['merchant_id' => $merchant_id]);

        $delivery_info->delivery_price_type = 'doordash';

        $delivery_info->save();

        $doorDashDeliveryZone = new DeliveryZone();

        $doorDashDeliveryZone->merchant_id = $merchant_id;

        $doorDashDeliveryZone->delivery_type = 'Regular';

        $doorDashDeliveryZone->name = 'Door Dash';

        $doorDashDeliveryZone->active = 'Y';

        $doorDashDeliveryZone->save();

        return $doorDashDeliveryZone;
    }
}



