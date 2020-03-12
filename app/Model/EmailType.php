<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailType extends Model {
    const GENERIC_EMAIL = 'generic_email';
    const RESELLER_FORM_1_TYPE = 'reseller_form_1';
    const RESELLER_FORM_1_REMINDER_TYPE = 'reseller_form_1_reminder';
    const MERCHANT_FORM_1_TYPE = 'merchant_form_1';
    const MERCHANT_FORM_1_REMINDER_TYPE = 'merchant_form_1_reminder';
    const MERCHANT_FORM_2_TYPE = 'merchant_form_2';
    protected $table = 'portal_email_types';
    protected $fillable = ['type'];

    /**
     * Get all emails of this type
     */
    public function emails() {
        return $this->hasMany('App\Model\Email');
    }

    /**
     * Get all the reminder periods for this email type
     */
    public function reminderPeriods() {
        return $this->hasMany('App\Model\ReminderPeriod');
    }

    /**
     * Get all email templates for this type
     * @return  HasMany | Builder
     */
    public function templates() {
        return $this->hasMany('App\Model\EmailTemplate');
    }

    /**
     * Gets the email type matching the type key provided
     * @param string $type
     * @return EmailType
     */
    public static function emailType($type){
        return self::where('type', $type)->first();
    }

    public static function genericType() {
        return self::emailType(self::GENERIC_EMAIL);
    }

    public static function resellerForm1Type() {
        return self::emailType(self::RESELLER_FORM_1_TYPE);
    }

    public static function resellerForm1ReminderType() {
        return self::emailType(self::RESELLER_FORM_1_REMINDER_TYPE);
    }

    public static function merchantForm1Type() {
        return self::emailType(self::MERCHANT_FORM_1_TYPE);
    }

    public static function merchantForm1ReminderType() {
        return self::emailType(self::MERCHANT_FORM_1_REMINDER_TYPE);
    }

    public static function merchantForm2Type() {
        return self::emailType(self::MERCHANT_FORM_2_TYPE);
    }
}
