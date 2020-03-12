<?php

namespace App\Model;

use \App\Service\Utility;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use App\Scopes\LogicalDelete;
use Carbon\Carbon;

class Lead extends BaseModel {
    protected $table = 'portal_leads';
    protected $fillable = ['guid', 'store_name', 'store_address1', 'store_address2', 'store_city',
        'store_state', 'store_zip', 'store_country', 'store_time_zone', 'store_email', 'store_phone_no', 'contact_name',
        'contact_title', 'contact_phone_no', 'contact_email', 'category', 'service', 'payment',
        'web_url', 'subdomain', 'grubbub', 'eat24', 'doordash', 'postmates', 'facebook',
        'chownum', 'yelp', 'google_places', 'groupon', 'ubereats', 'offer_id', 'email_send',
        'em', 'first_form_completion', 'second_form_completion', 'merchant_id'];

    protected $booleanYNAttributes = ['grubbub', 'eat24', 'doordash', 'postmates', 'facebook', 'chownum',
        'yelp', 'google_places', 'groupon', 'ubereats'];

    protected $rules = ['guid' => 'required|max:100', 'store_name' => 'required|max:100',
        'store_address1' => 'required|max:100', 'store_city' => 'required|max:100',
        'store_state' => 'required|max:2', 'store_zip' => 'required|digits:5',
        'store_country' => 'required|max:100', 'store_time_zone' => 'required|max:10',
        'store_email' => 'required|max:100|email',
        'store_phone_no' => 'required|digits:10', 'contact_name' => 'required|max:100',
        'contact_title' => 'required|max:100', 'contact_phone_no' => 'required|digits:10',
        'contact_email' => 'required|max:100|email', 'category' => 'required|max:100', 'service' => 'required',
        'payment' => 'required', 'subdomain' => 'required|max:60', 'offer_id' => 'required'];

    protected $attrNames = ['store_phone_no' => 'store phone number', 'contact_phone_no' => 'contact phone number'];

    protected $searchParams = ['created_at', 'updated_at', 'first_form_completion',
        'second_form_completion'];

    protected static function boot() {
        parent::boot();

        static::addGlobalScope(new LogicalDelete);
    }

    /**
     * Get the offer that owns the asset.
     */
    public function offer() {
        return $this->belongsTo('App\Model\Offer');
    }

    public function validate($data) {
        if (!isset($data["guid"])) {
            $data["guid"] = Uuid::uuid4()->toString();
        }
        $utility = new Utility();
        if (isset($data['store_phone_no'])) {
            $data['store_phone_no'] = $utility->replaceEverythingButNotNumber($data['store_phone_no']);
        }
        if (isset($data['contact_phone_no'])) {
            $data['contact_phone_no'] = $utility->replaceEverythingButNotNumber($data['contact_phone_no']);
        }
        return parent::validate($data);
    }

    public function fill(array $attributes) {
        if (!isset($attributes["guid"]) && !isset($this['guid'])) {
            $attributes["guid"] = Uuid::uuid4()->toString();
        }
        $utility = new Utility();
        if (isset($attributes['store_phone_no'])) {
            $attributes['store_phone_no'] = $utility->formatGoodTenDigitPhoneNumber($utility->
            replaceEverythingButNotNumber($attributes['store_phone_no']));
        }
        if (isset($attributes['contact_phone_no'])) {
            $attributes['contact_phone_no'] = $utility->formatGoodTenDigitPhoneNumber($utility->
            replaceEverythingButNotNumber($attributes['contact_phone_no']));
        }
        foreach ($this->booleanYNAttributes as $attr) {
            if (isset($attributes[$attr])) {
                $attributes[$attr] = $utility->convertBooleanYN($attributes[$attr]);
            }
        }
        return parent::fill($attributes);
    }

    public static function serviceTypes() {
        $reg = DB::select('describe portal_leads service')[0];
        $pieces = explode("'", $reg->Type);
        $pieces = array_slice($pieces, 1, count($pieces) - 2);
        return array_values(array_diff($pieces, [',']));
    }

    public static function paymentTypes() {
        $reg = DB::select('describe portal_leads payment')[0];
        $pieces = explode("'", $reg->Type);
        $pieces = array_slice($pieces, 1, count($pieces) - 2);
        return array_values(array_diff($pieces, [',']));
    }

    public static function pendingSignUpProcess($timeLapse, $timeUnit = 'minutes') {
        list($from, $to) = self::getDateRanges($timeLapse, $timeUnit);
        return self::whereNotNull('email_send')->whereNull('first_form_completion')
            ->whereBetween('email_send', [$from, $to])->get();
    }

    public static function pendingFillDataProcess($timeLapse, $timeUnit = 'minutes') {
        list($from, $to) = self::getDateRanges($timeLapse, $timeUnit);
        return self::whereNotNull('first_form_completion')->whereNull('second_form_completion')
            ->whereBetween('first_form_completion', [$from, $to])->get();
    }

    private static function getDateRanges($timeLapse, $timeUnit = 'minutes') {
        $from = Carbon::now();
        $to = Carbon::now();
        if ($timeUnit == 'minutes') {
            $from = $from->second(0)->subMinutes($timeLapse);
            $to = $to->second(59)->subMinutes($timeLapse);
        }
        if ($timeUnit == 'hours') {
            $from = $from->minute(0)->second(0)->subHours($timeLapse);
            $to = $to->minute(59)->second(59)->subHours($timeLapse);
        }
        if ($timeUnit == 'days') {
            $from = $from->hour(0)->minute(0)->second(0)->subDays($timeLapse);
            $to = $to->hour(23)->minute(59)->second(59)->subDays($timeLapse);
        }
        return [$from, $to];
    }
}
