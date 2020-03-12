<?php
/**
 * Created by PhpStorm.
 * User: diego.rodriguez
 * Date: 9/20/17
 * Time: 10:19 AM
 */

namespace App\Service;

use App\Model\EmailTemplate;
use App\Model\EmailType;
use App\Model\Lead;
use App\Model\Brand;
use App\Model\Property;
use App\Model\Skin;
use App\Model\Merchant;
use App\Model\Tax;
use App\Model\MerchantAdvancedOrderingInfo;
use App\Model\AdmMerchantPhone;
use App\Model\MerchantMenuFile;
use \DB;
use Carbon\Carbon;

class LeadService extends BaseService {

    /**
     * Creates and saves a lead
     *
     * @param array $data
     * @return Lead|bool the created Lead if successful, else false
     */
    public function createLead(array $data) {
        $this->clearErrors();
        $lead = new Lead();
        if (!$lead->validate($data)) {
            $this->errors = $lead->errors();
            return false;
        }
        $lead->fill($data);
        $lead->save();
        return $lead;
    }

    /**
     * @param Lead $lead
     * @return bool
     */
    public function createCustomerData(Lead $lead) {
        $this->clearErrors();
        if (!is_null($lead['first_form_completion'])) {
            $this->addError('This step was already completed for the customer!');
            return false;
        }
        try {
            DB::beginTransaction();

            $brand = Brand::fromLead($lead);
            $brand->save();

            $skin = Skin::fromLead($lead, $brand['brand_id']);
            $skin->save();

            $merchant = Merchant::fromLead($lead, $brand['brand_id']);
            $merchant->save();

            $admMerchantPhone = AdmMerchantPhone::fromLead($lead, $merchant['merchant_id']);
            $admMerchantPhone->save();

            $lead['first_form_completion'] = Carbon::now()->toDateTimeString();
            $lead['merchant_id'] = $merchant['merchant_id'];
            $lead->save();

            DB::commit();

            return ['brand' => $brand, 'skin' => $skin, 'merchant' => $merchant,
                'adm_merchant_phone' => $admMerchantPhone];
        } catch (\Exception $e) {
            $this->addError($e->getMessage());
            DB::rollback();
        }
        return false;
    }

    /**
     * @param Lead  $lead
     * @param array $data
     * @return bool
     */
    public function setupCustomerData(Lead $lead, array $data) {
        $this->clearErrors();
        $merchantId = $lead['merchant_id'];
        if (is_null($merchantId) || is_null($lead['first_form_completion'])) {
            $this->addError("The customer wasn't registered properly, please fill form 1 first.");
            return false;
        }
        if (!is_null($lead['second_form_completion'])) {
            $this->addError('This step was already completed for the customer!');
            return false;
        }
        /** @var Merchant $merchant */
        $merchant = Merchant::find($merchantId);
        if (is_null($merchant)) {
            $this->addError("The customer wasn't registered properly, please fill form 1 first.");
            return false;
        }
        try {
            DB::beginTransaction();
            $data = $this->sanitizeData($data);
            //save taxes
            $salesTax = Tax::fromCustomerData($merchant['merchant_id'], $data['sales_tax_rate']);
            $salesTax->save();

            $hasDelivery = $data['has_delivery'];
            if ($hasDelivery && isset($data['delivery_tax_rate'])) {
                $deliveryTax = Tax::fromCustomerData($merchant['merchant_id'], $data['delivery_tax_rate']);
                $deliveryTax->save();
            }

            //save advanced ordering
            $allowAdvancedOrders = $data['allow_advanced_orders'];
            if ($allowAdvancedOrders) {
                $advancedOrderingInfo = MerchantAdvancedOrderingInfo::create(['merchant_id' => $merchant['merchant_id'],
                    'max_days_out' => $data['advance_orders_max_days'],
                    'catering_minimum_lead_time' => 60]);
            }
            //save menu file records
            $this->saveMenuFiles($merchantId, $data['menu_file_urls']);
            //update merchant
            $merchant->setupCustomerData($data)->save();
            //update lead date
            $lead['second_form_completion'] = Carbon::now()->toDateTimeString();
            $lead->save();

            DB::commit();

            $res = ['merchant' => $merchant, 'sales_tax' => $salesTax];
            if ($hasDelivery && isset($deliveryTax)) {
                $res['delivery_tax'] = $deliveryTax;
            }
            if ($allowAdvancedOrders && isset($advancedOrderingInfo)) {
                $res['advanced_ordering_info'] = $advancedOrderingInfo;
            }
            return $res;
        } catch (\Exception $e) {
            $this->addError($e->getMessage());
            DB::rollback();
        }
        return false;
    }

    private function sanitizeData($data) {
        $data['allow_advanced_orders'] = isset($data['allow_advanced_orders']) && $data['allow_advanced_orders'] == true;
        $data['has_delivery'] = isset($data['has_delivery']) && $data['has_delivery'] == true;
        $data['allow_tipping'] = isset($data['allow_tipping']) && $data['allow_tipping'] == true;
        $data['allow_group_orders'] = isset($data['allow_group_orders']) && $data['allow_group_orders'] == true;
        return $data;
    }

    private function saveMenuFiles($merchantId, array $fileUrls) {
        foreach ($fileUrls as $url) {
            MerchantMenuFile::create(['merchant_id' => $merchantId, 'url' => $url]);
        }
    }

    /**
     * It enqueues an email to request the customer to sign up
     * to our services
     *
     * @param Lead $lead
     * @return bool
     */
    public function sendResellerFormEmail(Lead $lead) {
        $this->clearErrors();
        $emailService = new EmailService();
        $recipient = [$lead['contact_email'], $lead['store_email']];
        /** @var EmailTemplate $template */
        $template = EmailType::resellerForm1Type()->templates()->first();
        $url = (new Utility())->serverUrl();
        $recipient = array_unique($recipient);
        $result = $emailService->sendEmail('mail.customer_sign_up',
            ['lead' => $lead, 'content' => $template['message'], 'url' => $url],
            $template['subject'], $recipient, EmailType::RESELLER_FORM_1_TYPE,
            $template['sender_name'], $template['sender']);
        $this->errors = $emailService->errors();
        if (empty($this->errors)) {
            $lead->update(['email_send' => Carbon::now()]);
        }
        return $result;
    }

    /**
     * It enqueues an email to request the customer to complete its information
     *
     * to our services
     *
     * @param Lead $lead
     * @return bool
     */
    public function sendMerchantFormEmail(Lead $lead) {
        $this->clearErrors();
        $emailService = new EmailService();
        $recipient = [$lead['contact_email'], $lead['store_email']];
        /** @var EmailTemplate $template */
        $template = EmailType::merchantForm1Type()->templates()->first();
        $url = (new Utility())->serverUrl();
        $recipient = array_unique($recipient);
        $result = $emailService->sendEmail('mail.customer_setup',
            ['lead' => $lead, 'content' => $template['message'], 'url' => $url],
            $template['subject'], $recipient, EmailType::MERCHANT_FORM_1_TYPE,
            $template['sender_name'], $template['sender']);
        $this->errors = $emailService->errors();
        return $result;
    }

    /**
     * It enqueues an email to notify splickit team
     * that a customer has successfully completed its data setup
     *
     * @param Lead $lead
     * @return bool
     */
    public function notifyCustomerWasSetup(Lead $lead) {
        $this->clearErrors();
        $emailService = new EmailService();
        $recipient = Property::valueOf(Property::ONBOARDING_RECIPIENT_EMAIL) ??
            'support@yourcompany.com';
        $merchant = Merchant::find($lead['merchant_id']);
        $brand = Brand::find($merchant['brand_id']);
        $skin = Skin::where('brand_id', $brand['brand_id'])->first();
        $result = $emailService->sendEmail('mail.splickit_notification',
            ['lead' => $lead, 'merchant' => $merchant, 'brand' => $brand, 'skin' => $skin,],
            'New Client Ready for Setup',
            $recipient, EmailType::MERCHANT_FORM_2_TYPE,
            'Order140 Notification');
        $this->errors = $emailService->errors();
        return $result;
    }
}