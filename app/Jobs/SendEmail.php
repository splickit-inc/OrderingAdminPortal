<?php

namespace App\Jobs;

use App\Model\Email;
use App\Model\EmailStatus;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Message;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Mail\Mailer;

class SendEmail implements ShouldQueue {
    use InteractsWithQueue, Queueable, SerializesModels;
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /** @var $email Email */
    protected $email;
    protected $template;
    protected $data;
    protected $subject;
    protected $recipient;
    protected $sender;
    protected $senderName;

    /**
     * Create a new job instance.
     *
     * @param Email  $email    , this can't be null
     * @param string $template blade template name
     * @param array  $data     data to pass to the template
     * @param string $subject
     * @param string $recipient
     * @param string $senderName
     * @param string $sender
     * @throws \Exception if email is null
     */
    public function __construct(Email $email, $template, array $data, $subject, $recipient,
                                $senderName = 'Order140 Team',
                                $sender = 'noreply@yourcompany.com') {
        if (is_null($email)) {
            throw new \Exception("Email can't be null");
        }
        $this->email = $email;
        $this->template = $template;
        $this->data = $data;
        $this->subject = $subject;
        $this->recipient = $recipient;
        $this->sender = $sender;
        $this->senderName = $senderName;
    }

    /**
     * Execute the job.
     *
     * @param Mailer $mailer
     * @return void
     */
    public function handle(Mailer $mailer) {
        $mailer->send($this->template, $this->data, function (Message $message) {
            $message->from($this->sender, $this->senderName);
            $message->to($this->recipient);
            $message->subject($this->subject);
        });
        $this->email->update(['sent_at' => Carbon::now(), 'status' => EmailStatus::SENT]);
    }

    /**
     * The job failed to process.
     *
     * @param  Exception $exception
     * @return void
     */
    public function failed(Exception $exception) {
        $this->email->update(['status' => EmailStatus::ERROR]);
    }
}
