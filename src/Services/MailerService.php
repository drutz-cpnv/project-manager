<?php

namespace App\Services;

use App\Entity\Client;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class MailerService
{

    private array $emails = [];

    public function __construct(
        private UserRepository $userRepository
    )
    {}

    public function copilNewClient(Client $client)
    {
        $copils = $this->userRepository->findAllCopil();

        foreach ($copils as $copil) {
            $this->emails[] = (new TemplatedEmail())
                ->subject("Nouveau client")
                ->to($copil->getEmail())
                ->htmlTemplate('email/copil/new_client.html.twig')
                ->context([
                    'client' => $client
                ]);
        }

    }

}