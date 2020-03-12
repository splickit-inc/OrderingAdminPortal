<?php

namespace App\Service;

use \App\Model\Property;
use Illuminate\Support\Facades\Log;

class VioService  {

    protected $curl;

    public $positionAmount;
    const ROOT_URL = 'https://reports.value.io/reports';

    public function __construct() {
        $vioApiCredentials = Property::where('name', '=', 'vio_api_key')->first();

        $this->curl = curl_init();

        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('X-Api-Token: '.$vioApiCredentials->value ));
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
    }

    public function apiGetRequest($setGet = true) {
        if ($setGet) {
            $getQueryString = http_build_query($this->getVariables);
            $this->apiUrl = $this->apiUrl."?".$getQueryString;
        }

        curl_setopt($this->curl,CURLOPT_POST, 0);
        curl_setopt($this->curl, CURLOPT_URL, $this->apiUrl);
        $resp = curl_exec($this->curl);

        $this->getVariables = [];
        $this->rawResponse = $resp;
        return json_decode($resp);
    }


    public function getDailyDepositSummariesForDate($date = false) {
        try {

        if ($date) {
            $this->apiUrl = 'https://reports.value.io/reports.json?filter_report_type=propay_merchant_bank_deposit&page=1&search='.urlencode(date("n/j/Y", strtotime($date)));
        }
        else {
            $this->apiUrl = 'https://reports.value.io/reports.json?filter_report_type=propay_merchant_bank_deposit';
        }

        curl_setopt($this->curl,CURLOPT_POST, 0);
        curl_setopt($this->curl, CURLOPT_URL, $this->apiUrl);

            $resp = curl_exec($this->curl);

            if ($resp == '<html><body>You are being <a href="https://reports.value.io/reports.json?filter_report_type=propay_merchant_bank_deposit&amp;page=1">redirected</a>.</body></html>') {
                return false;
            }
            else {
                return json_decode($resp);
            }
        }
        catch (\Exception $e) {
            Log::error('Exception in accessing VIO Api with url '.$this->apiUrl);
            Log::error($e->getMessage());
        }


    }
}
