<?php
/**
 * Created by PhpStorm.
 * User: diego.rodriguez
 * Date: 11/15/17
 * Time: 3:12 PM
 */

namespace App\Service;

use App\Jobs\SendEmail;
use App\Model\Email;
use App\Model\EmailStatus;
use App\Model\EmailType;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;


class EmailService extends BaseService {
    use DispatchesJobs;

    /**
     * Sends an email trough the dispatch queue
     *
     * @param        $template
     * @param array  $data
     * @param        $subject
     * @param        $recipient
     * @param string $emailTypeKey
     * @param string $senderName
     * @param string $sender
     * @return bool true if the email sending was queued successfully, it doesn't mean the email
     *              was sent successfully tho
     */
    public function sendEmail($template, array $data, $subject, $recipient,
                              $emailTypeKey = EmailType::GENERIC_EMAIL,
                              $senderName = 'Order140 Team',
                              $sender = 'noreply@yourcompany.com') {
        return $this->safeExec(function () use (
            $template, $data, $subject, $recipient,
            $emailTypeKey, $senderName, $sender
        ) {
            $email = $this->saveEmailRecord($template, $data, $emailTypeKey, $recipient, $sender,
                $senderName, $subject);
            $email->update(['queued_at' => Carbon::now(), 'status' => EmailStatus::QUEUED]);
            $this->dispatch(new SendEmail($email, $template, $data, $subject,
                $recipient, $senderName, $sender));
        });
    }

    /**
     * Sends an email trough the dispatch queue
     *
     * @param Carbon $date delivery date. The job will stay in the queue until this date
     * @param        $template
     * @param array  $data
     * @param        $subject
     * @param        $recipient
     * @param string $emailTypeKey
     * @param string $senderName
     * @param string $sender
     * @return bool true if the email sending was queued successfully, it doesn't mean the email
     *                     was sent successfully tho
     */
    public function scheduleEmail(Carbon $date, $template, array $data, $subject, $recipient,
                                  $emailTypeKey = EmailType::GENERIC_EMAIL,
                                  $senderName = 'Order140 Team',
                                  $sender = 'noreply@yourcompany.com') {
        return $this->safeExec(function () use (
            $date, $template, $data, $subject, $recipient,
            $emailTypeKey, $senderName, $sender
        ) {
            $email = $this->saveEmailRecord($template, $data, $emailTypeKey, $recipient, $sender,
                $senderName, $subject);
            $email->update(['queued_at' => Carbon::now(), 'status' => EmailStatus::QUEUED]);
            $this->dispatch((new SendEmail($email, $template, $data, $subject,
                $recipient, $senderName, $sender))->delay($date));
        });
    }

    /**
     * @param $template
     * @param $data
     * @param $emailTypeKey
     * @param $recipient
     * @param $sender
     * @param $senderName
     * @param $subject
     *
     * @return Email
     */
    private function saveEmailRecord($template, $data, $emailTypeKey,
                                     $recipient, $sender, $senderName, $subject) {
        $emailContent = View::make($template)->with($data)->render();
        $emailType = EmailType::emailType($emailTypeKey);
        return Email::create(['recipient' => $recipient, 'sender' => $sender,
            'sender_name' => $senderName, 'subject' => $subject,
            'message' => $emailContent, 'priority' => 2, 'email_type_id' => $emailType['id']]);
    }
}