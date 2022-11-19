<?php

namespace App\EventSubscriber;

use App\Event\AddPersonneEvent;
use App\Service\MailService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PersonneEventSubscriber implements EventSubscriberInterface
{

    public function __construct(private MailService $mailer)
    {
    }

    public static function getSubscribedEvents() : array
    {
        // TODO: Implement getSubscribedEvents() method.
        return [
            AddPersonneEvent::ADD_PERSONNE_EVENT => ['onAddPersonneEvent', 3000],
        ];
    }

    public function onAddPersonneEvent  (AddPersonneEvent $event): void
    {
        $personne = $event->getPersonne();
        $mailMessage = $personne->getFirstname() . ' ' . $personne->getName() . " a été ajouté";
        $this->mailer->sendEmail(content: $mailMessage, subject: "Mail send from event subscriber");
    }
}