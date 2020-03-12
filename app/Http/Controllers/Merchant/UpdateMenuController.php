<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\SplickitApiCurlController;
use Illuminate\Http\Request;

class UpdateMenuController extends SplickitApiCurlController
{
    public function setMenuRelated(Request $request) {
        if (!$request->has('selected_menus') ||
            !$request->session()->has('current_merchant_id') ||
            $request->session()->get('current_merchant_id') == "") {
            return $this->errorResponse("invalid request", 404);
        }
        $selected_menus = $request->input('selected_menus');

        $current_merchant_id = session('current_merchant_id');

        $response = [];
        foreach ($selected_menus as $menu)
        {
            $this->api_endpoint = 'menus/'.$menu['menu_id'].'/merchants/'.$current_merchant_id;
            $this->data = ['merchant_menu_type'=>$menu['pivot']['merchant_menu_type']];

            $this->audit_data['auditable_id'] = $current_merchant_id;
            $this->audit_data['action'] = 'Update';
            $this->audit_data['auditable_type'] = 'Merchant-MenuMap';

            $response = $this->makeCurlRequest(true);

            array_push($response, $this->makeCurlRequest(true));
        }
        return response()->json($response);
    }
}
