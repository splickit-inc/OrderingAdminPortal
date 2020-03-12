<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ReminderPeriod extends Model implements Auditable  {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'portal_reminder_periods';
    protected $fillable = ['time_lapse', 'unit', 'email_type_id'];

    /**
     * Get the email type for for this reminder period
     */
    public function emailType() {
        return $this->belongsTo('App\Model\EmailType');
    }

    /**
     * Get the email template for this reminder period
     */
    public function emailTemplate() {
        return $this->belongsTo('App\Model\EmailTemplate');
    }

    /**
     * Gets the email reminder periods matching the type key provided
     *
     * @param string $type
     * @return array
     */
    public static function periodsFor($type) {
        $emailType = EmailType::emailType($type);
        if (is_null($emailType)) {
            return [];
        }
        return $emailType->reminderPeriods()->getResults();
    }
}
