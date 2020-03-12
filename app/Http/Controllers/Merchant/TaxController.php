<?php namespace App\Http\Controllers\Merchant;

use \Request;
use \App\Model\Tax;
use App\Model\FixedTax;
use \Exception;
use \App\Http\Controllers\Controller;
use \DB;

class TaxController extends Controller {

    //Load Tax Data to UI
    public function index() {
        $merchant_id = session('current_merchant_id');

        //Get Regular Rate Tax Info
        try {
            $sales_tax = Tax::where('merchant_id', '=', $merchant_id)
                            ->where('tax_group', '=', 1)->firstOrFail()->toArray();
        } catch (Exception $ex) {
            $sales_tax = ['locale' => 'State', 'locale_description' => null, 'rate' => null];
        }

        //Get Regular Rate Tax Info
        try {
            $delivery_tax = Tax::where('merchant_id', '=', $merchant_id)
                            ->where('tax_group', '=', 99 )->firstOrFail()->toArray();
        } catch (Exception $ex) {
            $delivery_tax = ['locale'=> 'Delivery', 'locale_description'=> null, 'rate'=>null];
        }

        //Get Fixed Rate Tax Info
        try {
            $fixed_tax = FixedTax::where('merchant_id', '=', $merchant_id)->firstOrFail()->toArray();
        } catch (Exception $ex) {
            $fixed_tax = ['name'=> null, 'description'=>null, 'amount'=>null];
        }

        return [
            "fixed_tax"=>$fixed_tax,
            "sales_tax"=>$sales_tax,
            "delivery_tax"=>$delivery_tax
        ];
    }

    public function updateSalesTax() {
        $data = Request::all();

        $merchant_id = session('current_merchant_id');

        $tax = Tax::firstOrNew(['merchant_id' => $merchant_id, 'tax_group' => 1]);

        $tax->locale = $data['locale'];
        $tax->locale_description = $data['locale_description'];
        $tax->rate = $data['rate'];

        $tax->save();

        return 1;
    }

    public function updateDeliveryTax() {
        $data = Request::all();

        $merchant_id = session('current_merchant_id');

        $tax = Tax::firstOrNew(['merchant_id' => $merchant_id, 'tax_group' => 99]);

        $tax->locale = $data['locale'];
        $tax->tax_group = 99;
        $tax->locale_description = $data['locale_description'];
        $tax->rate = $data['rate'];

        $tax->save();

        return 1;
    }

    public function updateFixedTax() {
        $data = Request::all();

        $merchant_id = session('current_merchant_id');

        $fixed_tax = FixedTax::firstOrNew(['merchant_id' => $merchant_id]);

        $fixed_tax->name = $data['name'];
        $fixed_tax->description = $data['description'];
        $fixed_tax->amount = $data['amount'];

        $fixed_tax->save();

        return 1;
    }
}



