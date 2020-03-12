<?php

namespace App\Http\Controllers;

use App\Model\Lead;
use App\Service\S3FileService;
use App\Service\StripeService;
use Illuminate\Http\Request;
use App\Service\LeadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;


class ProspectController extends Controller {

    const MAX_MENU_FILE_SIZE = 5242880;
    const VALID_MENU_FILE_TYPES = ['pdf', 'acrobat', 'jpg', 'jpeg',
        'png', 'bmp', 'bitmap'];

    /**
     * Stores a Brand, Skin, Merchant and user information created from a Lead.
     *
     * @param string $leadGuid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createMerchant($leadGuid, Request $request) {
        $data = $request->all();
        $chargeUUID = Uuid::uuid4()->toString();
        $amount = 232.23;
        $stripe = StripeService::withChargeUUID($chargeUUID);
        $charge = $stripe->chargeTheCardWithToken($data['token']['id'], $amount, "Charge for example with User ID and Offer etc");

        if ($charge['error']) {
            return response()->json($charge, 402);
        }

        $lead = Lead::where('guid', $leadGuid)->first();
        if (is_null($lead)) {
            return $this->errorResponse("Not found", 404);
        }
        $leadService = new LeadService();
        $res = $leadService->createCustomerData($lead);
        if ($res === false) {
            return $this->errorResponse($leadService->errors(), 422);
        }
        $emailRes = $leadService->sendMerchantFormEmail($lead);
        if ($emailRes === false) {
            //Log if email couldn't be enqueued for some weird reason
            Log::error(serialize($leadService->errors()));
        }
        $res['charge'] = $charge;
        return response()->json($res, 201);
    }

    /**
     * Sets up a new customer information.
     *
     * @param string $leadGuid
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setupCustomer($leadGuid, Request $request) {
        $lead = Lead::where('guid', $leadGuid)->first();
        if (is_null($lead)) {
            return $this->errorResponse("Not found", 404);
        }
        $data = $request->all();
        $leadService = new LeadService();
        $res = $leadService->setupCustomerData($lead, $data);
        if ($res === false) {
            return $this->errorResponse($leadService->errors(), 422);
        }
        $emailRes = $leadService->notifyCustomerWasSetup($lead);
        if ($emailRes === false) {
            //Log if email couldn't be enqueued for some weird reason
            Log::error(serialize($leadService->errors()));
        }
        return response()->json($res, 201);
    }

    /**
     * Uploads menu files for a lead
     *
     * @param string $leadGuid
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadMenu($leadGuid, Request $request) {
        $files = $request->file('menu_files');
        $lead = Lead::where('guid', $leadGuid)->first();
        if (is_null($lead)) {
            return $this->errorResponse("Not found", 404);
        }
        if (is_null($files)) {
            return $this->errorResponse("One or more files exceed the upload size limit (5MB per file)", 400);
        }
        if (count($files) > 5) {
            return $this->errorResponse("You shall not upload more than 5 files at once", 400);
        }
        $filePaths = [];
        /** @var $file UploadedFile */
        foreach ($files as $file) {
            if ($this->fileIsInvalid($file)) {
                return $this->errorResponse('The file: "' .
                    $file->getClientOriginalName() .
                    '" is bigger than the permitted 5MB. No files were uploaded', 400);
            }
            if (!$this->fileIsValidType($file)) {
                return $this->errorResponse('The file: "' .
                    $file->getClientOriginalName() .
                    '" is not in a permitted format. No files were uploaded', 400);
            }
            array_push($filePaths, $file->getRealPath());
        }
        $s3FileService = new S3FileService();
        $res = $s3FileService->uploadMenuFiles($lead, $files);
        if ($res === false) {
            return $this->errorResponse($s3FileService->errors(), 422);
        }
        return response()->json($res, 200);
    }

    /**
     * @param UploadedFile $file
     * @return bool
     */
    public function fileIsInvalid(UploadedFile $file) {
        // bigger than 5mb
        return is_null($file->getType()) || empty($file->getType()) ||
            is_null($file->getSize()) || empty($file->getSize()) ||
            $file->getSize() > self::MAX_MENU_FILE_SIZE;
    }

    /**
     * @param UploadedFile $file
     * @return bool
     */
    public function fileIsValidType(UploadedFile $file) {
        if (is_null($file->getMimeType())) {
            return false;
        }
        $mimeType = $file->getMimeType();
        foreach (self::VALID_MENU_FILE_TYPES as $validType) {
            if (strpos($mimeType, $validType) !== false) {
                return true;
            }
        }
        return false;
    }

    public function errorResponse($error, $status) {
        return response()->json(["errors" => $error, "status" => $status], $status);
    }
}
