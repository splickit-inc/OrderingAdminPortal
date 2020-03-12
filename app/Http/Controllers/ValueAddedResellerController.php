<?php namespace App\Http\Controllers;

use Request;
use \DB;
use \Cache;
use \App\Model\ValueAddedReseller;


class ValueAddedResellerController extends SplickitApiCurlController {

    public function all() {
        return ValueAddedReseller::get()->toArray();
    }

    public function create() {
        $data = Request::all();

        $value_added_reseller = new ValueAddedReseller();

        $value_added_reseller->name = $data['name'];

        $value_added_reseller->description = $data['description'];

        $value_added_reseller->save();
    }

    public function destroy($id) {
        return ValueAddedReseller::destroy($id);
    }
}