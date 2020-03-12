<?php namespace App\Http\Controllers\Merchant;

use \App\Http\Controllers\Controller;
use \DB;
use \App\Model\Merchant;
use \App\Service\Utility;
use Illuminate\Http\Request;

class GeneralInfoController extends Controller
{

    public function index()
    {
        $merchant_id = session('current_merchant_id');

        //Get Merchant Data
        $merchant = Merchant::with('businessBanking')->with('businessInfo')->find($merchant_id);
        $merchant = $merchant->toArray();

        $utility = new Utility();
        $lookups = ['state', 'time_zone', 'country', 'inactive_reason'];
        $lookup_values = $utility->getLookupValues($lookups);

        $us_timezones = [];
        $non_us_timezones = [];

        //We Want U.S. Timezones at top
        foreach ($lookup_values['time_zone'] as $time_zone) {
            if ((float)$time_zone->type_id_value < -4.5 && (float)$time_zone->type_id_value > -9) {
                $us_timezones[] = $time_zone;
            } else {
                $non_us_timezones[] = $time_zone;
            }
        }

        $lookup_values['time_zone'] = array_merge($us_timezones, $non_us_timezones);

        return ['merchant' => $merchant, 'lookup' => $lookup_values];
    }

    //Update for the Location Form
    public function updateLocation(Request $request)
    {
        $merchant_id = session('current_merchant_id');

        $data = $request->all();

        $merchant = Merchant::find($merchant_id);

        $merchant->name = $data['business_name'];

        $merchant->display_name = $data['display_name'];

        $merchant->address1 = $data['address1'];

        if (!empty($data['merchant_external_id'])) {
            $merchant->merchant_external_id = $data['merchant_external_id'];
        }
        if (isset($data['address2'])) {
            $merchant->address2 = $data['address2'];
        }

        $merchant->city = $data['city'];

        $merchant->state = $data['state'];

        $merchant->zip = $data['zip'];

        $merchant->country = $data['country'];

        $merchant->time_zone = $data['time_zone']['type_id_value'];

        $merchant->time_zone_string = $data['time_zone']['type_id_name'];

        //Store
        $merchant->shop_email = $data['shop_email'];

        $merchant->phone_no = $data['phone_no'];

        $merchant->fax_no = $data['fax_no'];

        $merchant->save();

        return 1;
    }

    public function updateConfig(Utility $utility, Request $request)
    {
        try {
            $data = $request->all();
            $merchant_id = session('current_merchant_id');
            $data = collect($data);
            $data = $data->map(function ($value, $key) use ($utility) {
                if (is_bool($value)) {
                    return $utility->convertBooleanYN($value);
                }
                return $value;
            });

            /** @var Merchant $merchant */
            $merchant = Merchant::find($merchant_id);
            $merchant->fill($data->all());
            $result = $merchant->save();

            return response()->json($result, 200);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), 404);
        }
    }

    public function updateMessages(Request $request)
    {
        $merchant_id = session('current_merchant_id');

        $data = $request->all();

        $merchant = Merchant::find($merchant_id);

        $merchant->custom_order_message = $data['custom_order_message'];

        $merchant->custom_menu_message = $data['custom_menu_message'];

        $merchant->save();

        return 1;
    }

    public function updateBusinessInfo(Request $request, Merchant $merchant)
    {
        try {
            $merchant_id = session('current_merchant_id');
            $data = $request->all();
            /** @var Merchant $merchant */
            $merchant = $merchant->find($merchant_id);
            $result = $merchant->updateBusinessInfo($data);
            return response()->json(['business_info' => $result], 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 404);
        }
    }

    public function updateBusinessBanking(Request $request, Merchant $merchant)
    {
        try {
            $merchant_id = session('current_merchant_id');
            $data = $request->all();
            /** @var Merchant $merchant */
            $merchant = $merchant->find($merchant_id);
            $result = $merchant->updateBusinessBanking($data);
            return response()->json(['business_banking' => $result], 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 404);
        }
    }
}



