<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model {
    protected $table = 'portal_email_templates';
    protected $fillable = ['key', 'sender', 'sender_name', 'cc', 'bcc',
        'subject', 'message', 'email_type_id'];

    /**
     * Get the email type for this email.
     */
    public function emailType() {
        return $this->belongsTo('App\Model\EmailType');
    }

    public function fill(array $attributes) {
        $attributes = $this->sanitizeArrayAttributes($attributes);
        return parent::fill($attributes);
    }

    private function sanitizeArrayAttributes(array $attributes) {
        foreach ($attributes as $attrKey => $value) {
            if (is_array($value)) {
                $attributes[$attrKey] = implode(';', $value);
            }
        }
        return $attributes;
    }
}
