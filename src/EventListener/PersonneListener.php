<?php

namespace App\EventListener;

use App\Event\AddPersonneEvent;
use App\Event\ListAllPersonneEvent;
use Psr\Log\LoggerInterface;

class PersonneListener
{
    public function __construct(Private LoggerInterface $logger)
    {
    }

    public function onPersonneAdd(AddPersonneEvent $event): void
    {
        $this->logger->debug("salut j'ecoute l'event personne.add pour la personne " . $event->getPersonne()->getName());
    }

    public function onListAllPersonne(ListAllPersonneEvent $event): void
    {
        $this->logger->debug("le nombre de personne est " . $event->getNbPersonne());
    }
    public function onListAllPersonne2(ListAllPersonneEvent $event): void
    {
        $this->logger->debug("le second listener du nombre de personne est " . $event->getNbPersonne());
    }

}