<?php namespace App\Http\Controllers;

use Request;
use \DB;

class CustomerServiceController extends Controller {
    public function index() {
        return view('customer_service');
    }
}



