<?php namespace App\Service;

use Illuminate\Support\Facades\Log;

class SplickitWebSkinningCurlService extends CurlService
{
    function __construct()
    {
        $this->CURLOPT_SSL_VERIFYPEER = false;
        $this->setMethodToPost();
    }

    public function makeRequest($url, $data = [], $headers)
    {


        return $this->formatResponse(($this->simpleCurlIt($url,$data,$headers)));
    }

    public function formatResponse($raw_result)
    {
        return json_decode($raw_result,true);
    }
}