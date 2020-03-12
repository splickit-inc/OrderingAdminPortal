<?php
/**
 * Created by PhpStorm.
 * User: diego.rodriguez
 * Date: 11/24/17
 * Time: 11:41 AM
 */

namespace App\Service;


use App\Model\EmailTemplate;
use App\Model\EmailType;
use App\Model\Lead;
use App\Model\ReminderPeriod;

class LeadRemainderService extends BaseService {

    public function reviewSignUpReminders() {
        $reminderPeriods = ReminderPeriod::periodsFor(EmailType::RESELLER_FORM_1_REMINDER_TYPE);
        foreach ($reminderPeriods as $reminderPeriod) {
            /** @var EmailTemplate $template */
            $template = $reminderPeriod->emailTemplate()->getResults();
            $leads = Lead::pendingSignUpProcess($reminderPeriod['time_lapse'], $reminderPeriod['unit']);
            foreach ($leads as $lead) {
                $this->sendResellerFormReminder($lead, $template);
            }
        }
    }

    /**
     * It enqueues an email to remind the customer to sign up
     * to our services
     *
     * @param Lead           $lead
     * @param  EmailTemplate $template
     * @return bool
     */
    private function sendResellerFormReminder(Lead $lead, $template) {
        $this->clearErrors();
        $emailService = new EmailService();
        $recipient = [$lead['contact_email'], $lead['store_email']];
        $url = (new Utility())->serverUrl();
        $recipient = array_unique($recipient);
        $result = $emailService->sendEmail('mail.customer_sign_up',
            ['lead' => $lead, 'content' => $template['message'], 'url' => $url],
            $template['subject'], $recipient,
            EmailType::RESELLER_FORM_1_REMINDER_TYPE,
            $template['sender_name'], $template['sender']);
        $this->errors = $emailService->errors();
        return $result;
    }

    public function reviewWelcomeReminders() {
        $reminderPeriods = ReminderPeriod::periodsFor(EmailType::MERCHANT_FORM_1_REMINDER_TYPE);
        /** @var ReminderPeriod $reminderPeriod */
        foreach ($reminderPeriods as $reminderPeriod) {
            /** @var EmailTemplate $template */
            $template = $reminderPeriod->emailTemplate()->getResults();
            $leads = Lead::pendingFillDataProcess($reminderPeriod['time_lapse'], $reminderPeriod['unit']);
            foreach ($leads as $lead) {
                $this->sendMerchantFormEmail($lead, $template);
            }
        }
    }

    /**
     * It enqueues an email to request the customer to complete its information
     *
     * @param Lead          $lead
     * @param EmailTemplate $template
     * @return bool
     */
    private function sendMerchantFormEmail(Lead $lead, $template) {
        $this->clearErrors();
        $emailService = new EmailService();
        $recipient = [$lead['contact_email'], $lead['store_email']];
        $url = (new Utility())->serverUrl();
        $recipient = array_unique($recipient);
        $result = $emailService->sendEmail('mail.customer_setup',
            ['lead' => $lead, 'content' => $template['message'], 'url' => $url],
            $template['subject'], $recipient,
            EmailType::MERCHANT_FORM_1_REMINDER_TYPE, $template['sender_name'], $template['sender']);
        $this->errors = $emailService->errors();
        return $result;
    }
}