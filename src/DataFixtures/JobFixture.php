<?php

namespace App\DataFixtures;

use App\Entity\Job;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class JobFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // liste de nom de métiers à ajouter
        $jobs = [
            'Développeur Web',
            'Développeur Mobile',
            'Intégrateur Web',
            'Graphiste',
            'Chef de projet Web',
            'Chef de projet Mobile',
            'Chef de projet Multimédia',
            'Community Manager',
            'Responsable Marketing',
            'Responsable Communication',
            'Responsable Ressources Humaines',
            'Responsable Administratif et Financier',
            'Responsable Qualité',
            'Directeur de la Communication',
            'Directeur des Ressources Humaines',
            'Directeur Administratif et Financier',
            'Directeur Général',
        ];

        // boucle sur les noms
        foreach ($jobs as $job) {
            $newjob = new Job();
            $newjob->setDesignation($job);
            $manager->persist($newjob);
        }

        // exécute les requêtes
        $manager->flush();
    }
}
