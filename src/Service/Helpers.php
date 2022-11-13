<?php

namespace App\Service;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class Helpers
{
    private $langue;
    public function __construct(private LoggerInterface $logger, private Security $security)
    {
    }

    public function sayCoucou() : string
    {
        $this->logger->info('coucou');
        return 'Coucou';
    }

    public function getUser() : UserInterface
    {
        return $this->security->getUser();
    }
}