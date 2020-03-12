<?php

namespace App\Http\Controllers\Marketing;


use App\Http\Controllers\SplickitApiCurlController;
use App\Model\Brand;
use App\Model\Skin;
use App\Service\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LoyaltyController extends SplickitApiCurlController
{

    function uploadLogo(Request $request, Brand $brand)
    {
        try {
            $url = '';
            if (!empty($request->brand_id) && !empty($request->file)) {
                $brand = $brand->find($request->brand_id);
                if (!empty($brand->brand_external_identifier)) {
                    $path = Storage::putFileAs($brand->brand_external_identifier . '/web/brand-assets', $request->file, 'loyalty_logo.png', 'public');
                    $url = Storage::url($path);
                    /** upload the file to the preview bucket too */
                    try {
                        Storage::disk('preview')->putFileAs($brand->brand_external_identifier . '/web/brand-assets', $request->file, 'loyalty_logo.png', 'public');
                    } catch (\Exception $exception) {
                        Log::error($exception->getMessage());
                    }
                    return response()->json(['brand_identifier' => $brand->brand_external_identifier, 'logo_url' => $url], 200);
                } else {
                    throw new \Exception('The brand does not have a configured external identifier, please check brand configuration');
                }
            }
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 404);
        }
    }

    function setLoyaltyStatus(Request $request, Brand $brand)
    {
        try {
            /** @var Brand $brand */
            $brand = $brand->with('loyaltyRule')->find($request->brand_id);

            if (!empty($brand)) {
                $brand->loyalty = $request->loyalty;
                $brand->save();
            }
            $response = [
                'brand' => $brand
            ];
            return response()->json($response, 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 404);
        }
    }

    function getBrandConfiguration($brand_id, Brand $brand, Skin $skinModel)
    {
        try {
            $this->api_endpoint = "brands/${brand_id}/loyalty";

            /** @var Brand $brand */
            $brand = $brand->find($brand_id);
            $loyaltyAmounts = $brand->loyaltyBehaviourAwardAmount()->get([
                'Loyalty_Award_Brand_Trigger_Amounts.trigger_value',
                'Loyalty_Award_Brand_Trigger_Amounts.loyalty_award_trigger_type_id',
                'Loyalty_Award_Brand_Trigger_Amounts.loyalty_award_trigger_type_id',
                'Loyalty_Brand_Behavior_Award_Amount_Maps.*'
            ]);
            $brand_info = $brand->toArray();
            $response = $this->makeCurlRequest();
            if (empty($response['error_code'])) {
                $brand_info['loyalty_rule'] = $response;
            } else {
                $brand_info['loyalty_rule'] = [
                    'brand_id' => $brand_id
                ];
            }

            $response = [
                'brand' => $brand_info,
                'loyalty_amounts' => $loyaltyAmounts
            ];

            /** @var Skin $skin */
            $skin = $skinModel->where('brand_id', '=', $brand_id)->first();
            if (!empty($skin)) {
                $response['rules_info'] = $skin->rules_info;
            }
            $logoPath = $brand->brand_external_identifier . '/web/brand-assets/loyalty_logo.png';
            if (!empty($brand->brand_external_identifier) && Storage::disk('s3')->exists($logoPath)) {
                Storage::setVisibility($logoPath, 'public');
                $logoURL = Storage::url($logoPath);
                $response['logo_url'] = $logoURL;
            }

            return response()->json($response, 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 404);
        }
    }

    function setBrandConfiguration($brand_id, Request $request, Brand $brand, Skin $skinModel, Utility $utility)
    {
        try {
            /** @var Brand $brand */
            $brand = $brand->find($brand_id);
            $data = $request->all();

            $this->api_endpoint = "brands/${brand_id}/loyalty/adjustloylatypoints";
            $this->setMethodPost();

            $this->data = array_intersect_key($data, array_flip([
                'loyalty_type',
                'starting_point_value',
                'earn_value_amount_multiplier',
                'cliff_value',
                'cliff_award_dollar_value',
                'charge_tax',
                'enabled'
            ]));
            if (!empty($this->data['charge_tax'])) {
                $this->data['charge_tax'] = $this->data['charge_tax'] == 'Y' ? 1 : 0;
            }

            $loyalty_rules = $this->makeCurlRequest();
            if (!empty($loyalty_rules['error_code'])) {
                return response()->json($loyalty_rules, $loyalty_rules['error_code']);
            }

            /** @var Skin $skin */
            $skin = $skinModel->where('brand_id', '=', $brand_id)->first();
            if (!empty($skin) && !empty($request->rules_info)) {
                $skin->update(['rules_info' => $request->rules_info]);
            }
            return response()->json($loyalty_rules, 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 404);
        }
    }

    function setBonusPointsDayConfiguration($brand_id, Request $request, Brand $brand)
    {
        try {
            /** @var Brand $brand */
            $brand = $brand->with('loyaltyRule')->find($brand_id);
            $data = $request->all();
            $loyaltyAmounts = $brand->setBonusPointsDayConfiguration($data);
            $response = [
                'brand' => $brand,
                'loyalty_amounts' => $loyaltyAmounts
            ];
            return response()->json($response, 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 404);
        }
    }

    function deleteBonusPointsDayConfiguration($brand_id, $bonus_id, Brand $brand)
    {
        try {
            /** @var Brand $brand */
            $brand = $brand->with('loyaltyRule')->find($brand_id);
            $loyaltyAmounts = $brand->deleteBonusPointsDayRecord($bonus_id);
            $response = [
                'brand' => $brand,
                'loyalty_amounts' => $loyaltyAmounts
            ];
            return response()->json($response, 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 404);
        }
    }
}