<?php namespace App\Http\Controllers;

use App\Service\SplickitAdminCurlService;
use Illuminate\Support\Facades\Log;
use App\Model\Audits;

class SplickitApiCurlController extends Controller {

    public $data;
    public $api_endpoint;
    public $api_service;

    public $audit_data;

    public $smaw_stamp;

    public function __construct() {
        $this->api_service = new SplickitAdminCurlService();
    }

    public function makeCurlRequest($make_audit_record = false) {
         $response = $this->processApiResponse($this->api_service->makeRequest($this->api_endpoint,$this->data));
        if ($make_audit_record) {
            $this->makeAuditRecord($response);
        }
        return $response;
    }

    public function processApiResponse($response) {
        if (isset($response['error'])) {
            Log::alert("We have a failure with a Order140 API request to endpoint ".$this->api_endpoint." and data ".json_encode($this->data));
            return $response['error'];
        } else if ( isset($response['data']['result']) && $response['data']['result'] == 'success') {
            $this->smaw_stamp = $response['stamp'];
            return $response['data'];
        } else if ( isset($response['data']) && $response['data'] != null) {
            $this->smaw_stamp = $response['stamp'];
            return $response['data'];
        } else {
            Log::alert(json_encode($response));
            Log::alert("We have a failure with a Order140 API request to endpoint ".$this->api_endpoint." and data ".json_encode($this->data));
            return array("error"=>array("error_message"=>"There was an unknown error on the request"));
        }
    }

    public function setMethodPost() {
        $this->api_service->setMethodToPost();
    }

    public function setMethodDelete() {
        $this->api_service->setMethodToDelete();
    }

    public function setMethodGet() {
        $this->api_service->setMethodToGet();
    }

    public function makeAuditRecord($response) {
        $audit = new Audits();

        $audit->new_values = json_encode($this->data);
        $audit->old_values = json_encode($response);

        $audit->event = $this->audit_data['action'];
        $audit->auditable_type = $this->audit_data['auditable_type'];
        $audit->stamp = $this->smaw_stamp ? $this->smaw_stamp : ' ';

        $audit->tags = 'smaw_api';

        if (isset($this->audit_data['auditable_id'])) {
            $audit->auditable_id = $this->audit_data['auditable_id'];
        }
        elseif(isset($this->audit_data['response_auditable_id'])) {
            $audit->auditable_id = $response[$this->audit_data['response_auditable_id']];
        }
        else {
            $audit->auditable_id = 0;
        }

        $audit->url = $this->api_endpoint;
        $audit->user_id = session('user_id');

        $audit->save();
    }
}