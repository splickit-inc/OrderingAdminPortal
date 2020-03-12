<?php

namespace App\Service;


use App\Model\EmailType;
use App\User;
use Illuminate\Auth\Passwords\PasswordBroker;
use App\Service\EmailService;

class UserService extends BaseService {

    /** @var EmailService $emailService */
    protected $emailService;
    /** @var EmailType $emailType */
    protected $emailType;

    public function __construct(EmailService $emailService, EmailType $emailType) {
    //        $emailType = new EmailType();
    //        $emailService = new EmailService();
        $this->emailService = $emailService;
        $this->emailType = $emailType;
    }

    /**
     * @param User $user
     * @return boolean
     * @throws \Exception
     */
    public function sendUserWelcomeEmail($user) {
        try {
            $this->clearErrors();
            $userInfo = $user->toArray();
            $recipients = [$userInfo['email']];
            $template = $this->emailType->templates()->first();
            $createPasswordURL = $this->getCreatePasswordLink($user);

            $result = $this->emailService->sendEmail(
                'mail.set_user_password_email',
                [
                    'content' => $template['message'],
                    'link' => $createPasswordURL
                ],
                $template['subject'],
                $recipients,
                EmailType::GENERIC_EMAIL,
                $template['sender_name'],
                $template['sender']
            );
            $this->errors = $this->emailService->errors();
            if (!empty($this->errors)) {
                throw new \Exception($this->errors, 404);
            }
            return $result;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param User $user
     * @return string
     * @throws \Exception
     */
    private function getCreatePasswordLink($user) {
        return url( "/#/password/create/" . $this->getPasswordResetToken($user));
    }

    /**
     * Create and Get the password token to reset the user password.
     *
     * @param User $user
     * @return string
     * @throws \Exception
     */
    private function getPasswordResetToken($user) {
        try {
            /** @var PasswordBroker $passwordBroker */
            $passwordBroker = app('auth.password.broker');
            return $passwordBroker->createToken($user);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}