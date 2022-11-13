<?php

namespace App\Service;

use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailService
{
    private $replyTo;
    public function __construct(private MailerInterface $mailer, $replyTo)
    {
        $this->replyTo = $replyTo;
    }

    public function sendEmail(
        $to = "sefsguclu@gmail.com",
        $content = "Le content test",
        $subject = "Le subject test"
    ): void
    {
        $email = (new Email())
            ->from('devsefa.noreply@gmail.com')
            ->to($to)
            ->replyTo($this->replyTo)
            ->subject($subject)
            ->html($content);
            $this->mailer->send($email);
    }
}