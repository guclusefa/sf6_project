<?php

namespace App\EventListener;

use App\Event\AddPersonneEvent;
use Psr\Log\LoggerInterface;

class PersonneListener
{
    public function __construct(Private LoggerInterface $logger)
    {
    }

    public function onPersonneAdd(AddPersonneEvent $event)
    {
        $this->logger->debug("salut j'ecoute l'event personne.add pour la personne " . $event->getPersonne()->getName());
    }
}