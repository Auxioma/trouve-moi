<?php

namespace App\Service\Email;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
final class EmailPropositionDevisService
{
    public function __construct(
        private MailerInterface $mailer,
    ) {}

    public function send(User $findArtisan): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($findArtisan->getEmail()))
            ->to('devis@trouvemoi.eu')
            ->subject('Un nouveau devis a été proposé')
            ->htmlTemplate('emails/proposition_devis/proposition_devis.html.twig')
            ->context([]);

        $this->mailer->send($email);
    }
}
