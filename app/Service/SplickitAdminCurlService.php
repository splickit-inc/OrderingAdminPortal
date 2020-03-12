<?php namespace App\Service;

use Illuminate\Support\Facades\Log;

class SplickitAdminCurlService extends CurlService
{
    function __construct()
    {
        $this->CURLOPT_SSL_VERIFYPEER = false;
    }

    public function makeRequest($endpoint,$data = [])
    {
        $url = env('ORDERING_API_URL').'app2/portal/'.$endpoint;
        return $this->formatResponse(($this->curlIt($url,$data)));
    }

    public function formatResponse($raw_result)
    {
        return json_decode($raw_result,true);
    }
}