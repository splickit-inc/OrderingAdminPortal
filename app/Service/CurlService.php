<?php namespace App\Service;

use \Log;

class CurlService
{
    /**
     * holds the curl info from the result
     *
     * @array
     */
    public $curl_info;

    /**
     * @var forces the method of the request
     */
    private $method;

    protected $CURLOPT_SSL_VERIFYPEER = true;

    public function curlIt($url,$data = null,$headers = array())
    {
        if ($ch = curl_init($url)) {
            if ($data) {
                $json = json_encode($data);
                curl_setopt($ch, CURLOPT_POSTFIELDS,$json );
                curl_setopt($ch, CURLOPT_POST, 1);
                $headers[] = 'Content-Type: application/json; charset=utf-8';
                $headers[] = 'Content-Length: '.strlen($json);
            } else {
                if ($this->getMethod() == 'POST') {
                    curl_setopt($ch, CURLOPT_POST, 1);
                }
                elseif ($this->getMethod() == 'DELETE') {
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                }
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_VERBOSE,1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST,'TLSv1');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->CURLOPT_SSL_VERIFYPEER);

            $raw_result = curl_exec($ch);
            Log::info("THE RAW RESULT FROM THE CURL: $raw_result");
            $this->curl_info = curl_getinfo($ch);
            curl_close($ch);
            return $raw_result;

        } else {
            Log::info("THERE WAS AN ERROR CONNECTING!!!!!!!!!!!");
            Log::error("THERE WAS AN ERROR CONNECTING!!!!!!!!!!!");
            return false;
        }
    }

    public function simpleCurlIt($url,$data = null,$headers = array())
    {
        if ($ch = curl_init($url)) {
            if ($data) {
                $json = json_encode($data);
                curl_setopt($ch, CURLOPT_POSTFIELDS,$json );
                curl_setopt($ch, CURLOPT_POST, 1);
                $headers[] = 'Content-Type: application/json; charset=utf-8';
                $headers[] = 'Content-Length: '.strlen($json);
            } else {
                if ($this->getMethod() == 'POST') {
                    curl_setopt($ch, CURLOPT_POST, 1);
                }
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//            curl_setopt($ch, CURLOPT_VERBOSE,1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST,'TLSv1');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->CURLOPT_SSL_VERIFYPEER);

            $raw_result = curl_exec($ch);
            Log::info("THE RAW RESULT FROM THE CURL: $raw_result");
            $this->curl_info = curl_getinfo($ch);
            curl_close($ch);
            return $raw_result;

        } else {
            Log::info("THERE WAS AN ERROR CONNECTING!!!!!!!!!!!");
            Log::error("THERE WAS AN ERROR CONNECTING!!!!!!!!!!!");
            return false;
        }

    }

    function getMethod()
    {
        return $this->method;
    }

    function setMethodToPost()
    {
        $this->method = 'POST';
    }

    function setMethodToDelete()
    {
        $this->method = 'DELETE';
    }

    function setMethodToGet()
    {
        $this->method = 'GET';
    }




}
