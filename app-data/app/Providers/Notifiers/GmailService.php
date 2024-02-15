<?php

declare(strict_types=1);

namespace App\Providers\Notifiers;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Throwable;

class GmailService implements IEmailNotifier
{
    public function __construct(
        private Mailer $mailer,
        private NotifiersEnvConfig $config
    ){}

    public function notify(object $parameters): object
    {
        $emailed = false;
        $errors = [];
        $user = $parameters->user;
        try {
            $email = (new Email())
                ->from($this->config->FROM_EMAIL)
                ->to($user->email)
                ->subject('You have been notified')
                ->html('<p>You have been notified</p>')
                ->addPart(new DataPart(new File($parameters->files[0])));
            $this->mailer->send($email);
            $emailed = true;
        } catch(\Error|\Exception|Throwable|TransportExceptionInterface $e) {
            $errors[] = $e->getMessage();
            $errors[] = 'Could not send email';
        }
        return (object)[
            'result' => $emailed ? ["Emailed"] : [],
            'errors' => $errors,
        ];
    }
}
