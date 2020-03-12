<?php

namespace App\Service;

use Ramsey\Uuid\Uuid;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Error;

class StripeService {
    protected $chargeUUID;

    /**
     * StripeService constructor.
     */
    public function __construct() {
        $this->chargeUUID = Uuid::uuid4()->toString();
    }

    public static function withChargeUUID($chargeUUID) {
        $instance = new self();
        $instance->chargeUUID = $chargeUUID;
        return $instance;
    }

    public function chargeTheCardWithToken($token, $amount, $description, $currency = "usd") {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        try {
            $charge = Charge::create([
                "amount" => $amount * 100, //we need to multiply by 100 because the value is on cents
                "currency" => $currency,
                "source" => $token, // obtained with Stripe.js
                "description" => $description      //"Charge for example with User ID and Offer etc" this will show on stripe dashboard and on the charge invoice
            ], [
                "idempotency_key" => $this->chargeUUID, //This id will use to try again the charge if there is network issues that breaks the first request, suggest using V4 UUIDs
                // we can send the information without this value if we don't wanna handler it.
                // this number must be changed everytime to do a new charge or use the same when the request fail
                // This will prevent to charge twice to the client if there is a problem on the comunication and we need to handle that on the catch error to try again the charge
            ]);
            return $charge;
        } catch (Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            //Use dis when something happen with the card or the owner Bank
            $body = $e->getJsonBody();
            $err = $body['error'];

            return [
                'error' => "The card was declined",
                'httpStatus' => $e->getHttpStatus(),
                'type' => $err['type'],
                'code' => $err['code'],
                'message' => $err['message'],
                'variable' => $err
            ];
        } catch (Error\RateLimit $e) {
            // Too many requests made to the API too quickly
            return $this->getJsonBody($e);
        } catch (Error\InvalidRequest $e) {
            // Invalid parameters were supplied to Stripe's API
            return $this->getJsonBody($e);
        } catch (Error\Authentication $e) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            return $this->getJsonBody($e);
        } catch (Error\ApiConnection $e) {
            // Network communication with Stripe failed
            return $this->getJsonBody($e);
        } catch (Error\Base $e) {
            // Display a very generic error to the user, and maybe send
            // yourself an email
            return $this->getJsonBody($e);
        } catch (\Exception $e) {
            // Something else happened, completely unrelated to Stripe
            return ['error' => $e];
        }

        /* Example Response when submit a valid card charge
            amount: 2200
            amount_refunded: 0
            application: null
            application_fee:null
            balance_transaction:"txn_1BdosyFju3lruwutwt6li6KI"
            captured:true
            created:1514419475
            currency:"usd"
            customer:null
            description:"Charge for example with User ID and Offer etc"
            destination:null
            dispute:null
            failure_code:null
            failure_message:null
            fraud_details:[]
            id:"ch_1BdosxFju3lruwutvtJzkFm9"                        //** This is the id that will save and show on the stripe dashboard
            invoice:null
            livemode:false
            metadata:[]
            object:"charge"
            on_behalf_of:null
            order:null
            outcome:{network_status: "approved_by_network", reason: null, risk_level: "normal",…}
            paid:true
            receipt_email:null
            receipt_number:null
            refunded:false
            refunds:{object: "list", data: [], has_more: false, total_count: 0,…}
            review:null
            shipping:null
            source:{id: "card_1BdossFju3lruwutpJUbV9za", object: "card", address_city: null, address_country: null,…}
            source_transfer:null
            statement_descriptor:null
            status:"succeeded"
            transfer_group:null
         */
    }

    protected function getJsonBody($e) {
        $body = $e->getJsonBody();
        $err = $body['error'];
        return $err;
    }
}