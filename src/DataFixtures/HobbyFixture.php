<?php

namespace App\DataFixtures;

use App\Entity\Hobby;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HobbyFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // tableau de hobbies à ajouter
        $hobbies = [
            'Cinéma',
            'Lecture',
            'Musique',
            'Voyage',
            'Sport',
            'Jeux vidéo',
            'Informatique',
            'Cuisine',
            'Jardinage',
            'Bricolage',
            'Décoration',
            'Couture',
            'Tricot',
            'Peinture',
            'Photographie',
            'Cyclisme',
            'Natation',
            'Tennis',
            'Football',
            'Basketball',
            'Volleyball',
            'Randonnée',
        ];

        // boucle sur les noms
        foreach ($hobbies as $hobby) {
            $newhobby = new Hobby();
            $newhobby->setDesignation($hobby);
            $manager->persist($newhobby);
        }

        // exécute les requêtes
        $manager->flush();
    }
}
