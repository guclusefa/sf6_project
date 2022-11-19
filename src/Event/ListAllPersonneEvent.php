<?php

namespace App\Event;

use App\Entity\Personne;
use Symfony\Contracts\EventDispatcher\Event;

class ListAllPersonneEvent extends Event
{
    public const LIST_ALL_PERSONNE_EVENT = 'personne.list.all';

    public function __construct(Private int $nbPersonne)
    {
    }

    public function getNbPersonne(): int
    {
        return $this->nbPersonne;
    }
}