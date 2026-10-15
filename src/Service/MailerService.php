<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(string $to, string $subject, array $content): void
    {
        $email = (new TemplatedEmail())
            ->from('newsletter@yourdomain.com')
            ->to($to)
            ->subject($subject)
            ->htmlTemplate('newsletter/newsletter.html.twig')
            ->context([
                'videogames' => $content,
            ]);
        $this->mailer->send($email);
    }
}