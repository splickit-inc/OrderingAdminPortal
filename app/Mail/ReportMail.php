<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailer as MailerContract;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $content;
    private $attachment;
    public $frequency;
    public $report_name;

    /**
     * Create a new message instance.
     * @param $content
     * @param $attachment
     * @param $frequency
     * @param $report_name
     * @return void
     */
    public function __construct($report_name, $frequency, $content, $attachment)
    {
        $this->content = $content;
        $this->attachment = $attachment;
        $this->frequency = $frequency;
        $this->report_name = $report_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (!empty($this->attachment)) {
            return $this->from('support@yourcompany.com')->subject('Reporting')
                ->view('mail.report')->attach($this->attachment, [
                    'as' => 'report-' . $this->frequency . '-' . Carbon::now()->toDateString() .'.csv',
                    'mime' => 'text/csv'
                ]);
        }

        return $this->from('support@yourcompany.com')->subject('Reporting')
            ->view('mail.report')->with(['errors' => 'The report is empty, it does not have records for now.']);
    }

    public function send(MailerContract $mailer)
    {
        parent::send($mailer); // TODO: Change the autogenerated stub
        if (!empty($this->attachment)) {
            unlink($this->attachment);
        }
    }
}
