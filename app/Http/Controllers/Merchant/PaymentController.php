<?php

namespace App\Http\Controllers\Merchant;


use App\Http\Controllers\Controller;
use App\Http\Controllers\SplickitApiCurlController;
use App\Model\Merchant;
use App\Model\VioDailyDepositSummaries;
use App\Service\VioService;
use App\Model\PaymentMerchantApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PaymentController extends SplickitApiCurlController
{
    public function getPaymentInformation(Merchant $merchantModel)
    {
        try {
            $merchant_id = session('current_merchant_id');
            $merchant = $merchantModel->with('paymentServiceBusiness')->with('paymentServiceOwner')->find($merchant_id);

            if (sizeof($merchant->paymentServiceBusiness->toArray()) > 1) {
                $initial_submission = false;
            }
            else {
                $initial_submission = true;
            }

            $response = [
                'business_information' => $merchant->paymentServiceBusiness,
                'payment_service_owner' => $merchant->paymentServiceOwner,
                'initial_submission' => $initial_submission
            ];

            return response()->json($response, 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 404);
        }
    }

    public function updateBusinessInformation(Request $request, Merchant $merchantModel)
    {
        try {
            $merchant_id = session('current_merchant_id');
            /** @var Merchant $merchant */
            $merchant = $merchantModel->find($merchant_id);
            $result = $merchant->updatePaymentBusinessInformation($request->all());
            return response()->json($result, 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 404);
        }
    }

    public function updateOwnerInformation(Request $request, Merchant $merchantModel)
    {
        try {
            $merchant_id = session('current_merchant_id');
            /** @var Merchant $merchant */
            $merchant = $merchantModel->find($merchant_id);

            $db_data = $request->all();
            $result = $merchant->updatePaymentOwnerInformation($db_data);

            $this->api_endpoint = 'payments/merchant_account';

            $this->data['merchant_id'] = $merchant_id;
            $this->data['first_name'] = $db_data['primary_owner_first_name'];
            $this->data['last_name'] = $db_data['primary_owner_last_name'];

            $this->data['date_of_birth'] = $db_data['primary_owner_dob'];
            $this->data['social_security_number'] = $db_data['primary_owner_ss'];
            $this->data['phone'] = $db_data['primary_owner_phone'];
            $this->data['business_legal_name'] = $db_data['business_name'];
            $this->data['doing_business_as'] = $db_data['dba'];
            $this->data['ein'] = $db_data['fed_tax_id'];
            $this->data['bank_account_account_number'] = $db_data['bank_account_number'];
            $this->data['bank_account_routing_number'] = $db_data['bank_routing_number'];

            $response = $this->makeCurlRequest();

//            if (isset($response['id'])) {
//                $paymentMerchantService = PaymentMerchantApplication::firstOrNew(['merchant_id'=>$merchant_id]);
//                $paymentMerchantService->successful_api_upload = 'Y';
//                $paymentMerchantService->save();
//            }

            $merchant = $merchantModel->with('paymentServiceOwner')->find($merchant_id);

            return ['smaw_response'=>$response, 'payment_application'=>$merchant->paymentServiceOwner];
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 404);
        }
    }

    public function uploadOwnerDocuments(Request $request, Merchant $merchantModel)
    {
        try {
            $response = [];
            $merchant_id = session('current_merchant_id');
            if ($request->hasFile('licenseFile') && $request->file('licenseFile')->isValid()) {
                $file = $request->file('licenseFile');
                $path = Storage::disk('MerchantProcessingDocuments')->putFileAs('OwnerInfo/' . $merchant_id, $file, 'driver_license.png', 'public');
                $response['primary_owner_drivers_license_url'] = Storage::disk('MerchantProcessingDocuments')->url($path);

                $paymentMerchantService = PaymentMerchantApplication::firstOrNew(['merchant_id'=>$merchant_id]);
                $paymentMerchantService->primary_owner_drivers_license_url = $response['primary_owner_drivers_license_url'];
                $paymentMerchantService->save();

            }

            if ($request->hasFile('voidedCheckFile') && $request->file('voidedCheckFile')->isValid()) {
                $file = $request->file('voidedCheckFile');
                $path = Storage::disk('MerchantProcessingDocuments')->putFileAs('OwnerInfo/' . $merchant_id, $file, 'voided_check.png', 'public');
                $response['primary_owner_void_check_url'] = Storage::disk('MerchantProcessingDocuments')->url($path);

                $paymentMerchantService = PaymentMerchantApplication::firstOrNew(['merchant_id'=>$merchant_id]);
                $paymentMerchantService->primary_owner_void_check_url = $response['primary_owner_void_check_url'];
                $paymentMerchantService->save();
            }
            return response()->json($response, 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 404);
        }
    }

    public function downloadVioDailySummaries()
    {
        try {
            $vioService = new VioService();
            // Start date
            $current_date = '2019-11-21';
            // End date
            $end_date = date('Y-m-d');

            while (strtotime($current_date) <= strtotime($end_date)) {
                $dailySummaries = $vioService->getDailyDepositSummariesForDate($current_date);

                $this->saveDailySummaries($dailySummaries);
                $current_date = date ("Y-m-d", strtotime("+1 day", strtotime($current_date)));
            }
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 404);
        }
    }

    public function saveDailySummariesToday() {
        $vioService = new VioService();
        $dailySummaries = $vioService->getDailyDepositSummariesForDate(date("Y-m-d"));
        $this->saveDailySummaries($dailySummaries);
    }

    protected function saveDailySummaries($dailySummaries) {
        if (!isset($dailySummaries->data)) {
            foreach ($dailySummaries as $dailySummary) {
                try {

                    $dailySummaryCheck = VioDailyDepositSummaries::where('trans_id', '=', (int) $dailySummary->Trans_Id)->first();

                    if (!$dailySummaryCheck) {
                        $newDailySummary = new VioDailyDepositSummaries();

                        $newDailySummary->fees = preg_replace("/[^0-9.]/", "", $dailySummary->Fees);
                        $newDailySummary->amount = preg_replace("/[^0-9.]/", "", $dailySummary->Amount);
                        $newDailySummary->details = $dailySummary->Details;
                        $newDailySummary->sweep_id = $dailySummary->SweepId;
                        $newDailySummary->attn_number = $dailySummary->att_num;
                        $newDailySummary->inv_number = $dailySummary->inv_num;
                        $newDailySummary->trans_id = $dailySummary->Trans_Id;
                        $newDailySummary->identifier = $dailySummary->Identifier;
                        $newDailySummary->report_date = $dailySummary->ReportDate;
                        $newDailySummary->gross_amount = preg_replace("/[^0-9.]/", "", $dailySummary->GrossAmount);
                        $newDailySummary->vio_account_number = $dailySummary->account_num;
                        $newDailySummary->ach_return_code = $dailySummary->AchReturnCode;
                        $newDailySummary->transaction_type = $dailySummary->TransactionType;
                        $newDailySummary->transaction_date_time = date('Y-m-d H:m',strtotime($dailySummary->TransactionDateTime));
                        $newDailySummary->charge_back_response_code = $dailySummary->ChargeBackResponeCode;
                        $newDailySummary->return_code_description = $dailySummary->ReturnCodeDescription;
                        $newDailySummary->response_code_description = $dailySummary->ResponeCodeDescription;

                        $newDailySummary->save();
                    }

                }
                catch (\Exception $exception) {
                    Log::error('Exception on Trying to Save VIO Daily Summary record '.json_encode($dailySummary));
                    Log::error($exception->getMessage());
                }

            }

        }
    }

    public function dailyDepositReport() {

        $paymentMerchantApplication = PaymentMerchantApplication::where('merchant_id','=', session('current_merchant_id'))->first();

        return VioDailyDepositSummaries::where('vio_account_number','=',$paymentMerchantApplication->vio_account_number)
                                ->orderBy('report_date', 'desc')
                                ->paginate(10);
    }
}