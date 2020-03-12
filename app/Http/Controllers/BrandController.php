<?php namespace App\Http\Controllers;


use App\Model\Brand;
use App\Model\ObjectOwnership;
use App\Model\Skin;
use App\Model\SkinMerchantMap;
use App\Model\PortalBrandLookupMap;
use App\Model\PortalLookupHierarchy;
use App\Service\Utility;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Service\CurlService;

class BrandController extends SplickitApiCurlController {

    public function searchBrands(Request $request, Brand $brand) {
        $search_value = $request->value;
        $fields = $request->fields;
        $search_result = $brand->searchBrands($fields, $search_value);
        return response()->json(['brands' => $search_result], 200);
    }

    public function getAllBrands(Request $request, Brand $brand) {
        $data = $request->all();
        return $brand->allBrands();
    }

    public function createBrand(Request $request) {
        $data = $request->all();
        $utility = new Utility();

        try {
            //New Brand
            $new_brand = new Brand();

            $new_brand->brand_name = $data['name'];

            $s3_name = $string = str_replace(' ', '', strtolower(preg_replace("/[^A-Za-z0-9 ]/", "", $data['name'])));

            $external_identifier = 'com.yourbiz.' . $s3_name;

            $new_brand->brand_external_identifier = $external_identifier;

            $new_brand->production = $utility->convertBooleanYN($data['production']);

            $new_brand->last_orders_displayed = $data['last_orders_displayed'];

            $new_brand->active = $utility->convertBooleanYN($data['active']);

            $new_brand->allows_tipping = $utility->convertBooleanYN($data['allows_tipping']);

            $new_brand->allows_in_store_payments = $utility->convertBooleanYN($data['allows_in_store_payments']);

            $new_brand->nutrition_flag = $data['nutrition_flag'];

            $new_brand->support_email = $data['support_email'];

            $new_brand->save();

            $order_delivery_lookup = new PortalLookupHierarchy();
            $order_delivery_lookup->brand_id = $new_brand->brand_id;
            $order_delivery_lookup->lookup_driver = 'B';
            $order_delivery_lookup->lookup_type_id_field = 'order_del_type';
            $order_delivery_lookup->save();


            foreach ($data['order_delivery_methods'] as $order_delivery_method) {
                $order_delivery_organization_lookup_map = new PortalBrandLookupMap();

                $order_delivery_organization_lookup_map->type_id_field = 'order_del_type';
                $order_delivery_organization_lookup_map->lookup_id = $order_delivery_method['lookup_id'];
                $order_delivery_organization_lookup_map->brand_id = $new_brand->brand_id;
                $order_delivery_organization_lookup_map->save();
            }

            $payment_type_lookup = new PortalLookupHierarchy();
            $payment_type_lookup->brand_id = $new_brand->brand_id;
            $payment_type_lookup->lookup_driver = 'B';
            $payment_type_lookup->lookup_type_id_field = 'Splickit_Accepted_Payment_Types';
            $payment_type_lookup->save();

            foreach ($data['payment_types'] as $payment_type) {
                $payment_type_lookup_map = new PortalBrandLookupMap();

                $payment_type_lookup_map->type_id_field = 'Splickit_Accepted_Payment_Types';
                $payment_type_lookup_map->lookup_id = $payment_type['lookup_id'];
                $payment_type_lookup_map->brand_id = $new_brand->brand_id;
                $payment_type_lookup_map->save();
            }

            $web_skin = new Skin();

            $web_skin->brand_id = $new_brand->brand_id;

            $web_skin->external_identifier = $external_identifier;

            $web_skin->in_production = $utility->convertBooleanYN($data['production']);

            $web_skin->skin_name = $data['name'];

            $web_skin->skin_description = $data['name'];

            $web_skin->public_client_id = '';
            $web_skin->iphone_certificate_file_name = '';

            $web_skin->lat = 0;
            $web_skin->lng = 0;
            $web_skin->zip = 0;

            $web_skin->save();

            //Create Skin Public Key with SMAW
            $this->api_endpoint = 'brands/'.$new_brand->brand_id.'/createpublickey';

            $this->setMethodPost();

            $this->data['skin_id'] = $web_skin->skin_id;

            $response = $this->makeCurlRequest();

            $new_object_ownership = new ObjectOwnership();

            $new_object_ownership->user_id = Auth::user()->id;
            $new_object_ownership->organization_id = session('user_organization_id');
            $new_object_ownership->object_type = 'brand';
            $new_object_ownership->object_id = $new_brand->brand_id;

            $new_object_ownership->save();

        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 404);
        }
    }

    public function editBrand($brand_id, Request $request, Brand $brand) {
        $data = $request->all();

        $brand_existing_record = Brand::find($brand_id);

        if ($brand_existing_record->active != $data['active']) {
            $this->bustBrandCache($brand_id);
        }

        $result = $brand->editBrand($brand_id, $data);
        if ($result) {
            return response()->json(['brand' => $result], 200);
        } else {
            return response()->json('error', 404);
        }
    }

    public function bustBrandCache($brand_id) {
        $this->api_endpoint = 'brands/'.$brand_id.'/flushcache';

        $this->audit_data['action'] = 'Update';
        $this->audit_data['auditable_type'] = 'Brand Cache';

        $response = $this->makeCurlRequest(true);

    }

    /**
     * Display the specified resource.
     *
     * @param  string $id
     * @return \Illuminate\Http\Response
     */
    public function getMerchants($id) {
        /** @var Brand $brand */
        $brand = Brand::find($id);
        if (is_null($brand)) {
            return response()->json(["errors" => "Not found", "status" => 404], 404);
        }
        $merchants = $brand->merchants(session('user_visibility'));
        return $merchants;
    }

    public function getSkinRelatedToMerchant($brand_id, $merchant_id, SkinMerchantMap $skinMerchantMap) {
        return $skinMerchantMap->getRelatedSkinsForMerchantBrand($merchant_id, $brand_id)->get();
    }

    public function getSkinsByBrand($brand_id, Skin $skin) {
        return $skin->getSkinsByBrand($brand_id)->get();
    }

    public function getByFirstLetter($letter, Brand $brand) {
        $result = $brand->getByFirstLetter($letter);
        return response()->json(['brands' => $result], 200);
    }

    public function getBrand($brand_id, Brand $brand) {
        $brand = $brand->find($brand_id);
        if(!$brand)
            return $this->errorResponse("Not Found", 404);
        return response()->json(['brand' => $brand], 200);
    }

    public function getCurrentBrand(Brand $brand)
    {
        $brand_id = session('brand_manager_brand');
        $brand = $brand->find($brand_id);
        if(!$brand)
            return $this->errorResponse("Not Found", 404);
        return response()->json(['brand' => $brand], 200);
    }
}
