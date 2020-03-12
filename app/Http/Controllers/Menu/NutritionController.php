<?php namespace App\Http\Controllers\Menu;

use Request;
use \DB;
use \Cache;

use \App\Http\Controllers\SplickitApiCurlController;

class NutritionController extends SplickitApiCurlController {

    public function index() {
        return view('menu');
    }

    public function menuNutritionGrid($merchant_id = false) {
        $menu_id = session('current_menu_id');

        $this->api_endpoint = 'menus/' . $menu_id . '/getpricelist?merchant_id='.$merchant_id;
        $this->api_endpoint = '/app2/portal/menus/'.$menu_id.'/nutrition';
        $this->setMethodGet();
        $response = $this->makeCurlRequest();

        $menu_types = [];

        foreach ($response['menu_types'] as $index=>$menu_type) {
            $offerings = [];
            foreach ($menu_type as $offering_name=>$offering) {
                $offerings[] = ['offering_label'=>$offering_name, 'offering_data'=>$offering];
            }
            $menu_types[] = ['menu_type_name' => $index, 'offerings'=> $offerings, 'opened'=>false];
        }

        return $menu_types;
    }

    public function updateMenuOfferingNutritionInfo() {
        $menu_id = session('current_menu_id');
        $this->data = Request::all();

        //$this->data = json_decode('{"id":"1044","item_id":"304118","size_id":"95128","serving_size":"16","calories":"999","calories_from_fat":"490","total_fat":"15.00","saturated_fat":"8.00","trans_fat":"0.00","cholesterol":"33.00","sodium":"173.00","total_carbohydrates":"74.00","dietary_fiber":"0.00","sugars":"53.00","protein":"19.00"}');

        $this->api_endpoint = '/app2/portal/menus/'.$menu_id.'/nutrition';
        $this->setMethodPost();
        $response = $this->makeCurlRequest();
    }
}