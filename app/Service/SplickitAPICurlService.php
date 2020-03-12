<?php namespace App\Service;


class SplickitAPICurlService extends CurlService
{

    public function makeRequest($endpoint,$data = [])
    {
        $url = env('ORDERING_API_URL').'app2/apiv2/'.$endpoint;
        $headers = ['Accept: application/json'];
        return $this->formatResponse($this->curlIt($url,$data,$headers));
    }

    public function formatResponse($raw_result)
    {
        return json_decode($raw_result,true);
    }
}
