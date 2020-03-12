<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\EmailService;
use Mail;

class MailController extends Controller {

    public function __construct() {
    }

    public function index() {
        return view('mail.sendmail');
    }

    public function sendMail(Request $request) {
        $body = ['message' => $request->message];
        $emailService = new EmailService();
        $emailService->sendEmail('mail.mail', ['body' => $body], "Order140 inform",
            $request->email, 'Support', 'noreply@yourcompany.com');
        return redirect('sendmail');

    }

}