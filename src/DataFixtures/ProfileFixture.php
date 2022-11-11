<?php

namespace App\DataFixtures;

use App\Entity\Profile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProfileFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // liste de reseaux sociaux à ajouter
        $profiles = [
            ['facebook.com', 'Facebook'],
            ['twitter.com', 'Twitter'],
            ['linkedin.com', 'LinkedIn'],
            ['instagram.com', 'Instagram'],
            ['pinterest.com', 'Pinterest'],
            ['youtube.com', 'YouTube'],
            ['tiktok.com', 'TikTok'],
            ['snapchat.com', 'Snapchat'],
            ['twitch.com', 'Twitch'],
            ['dailymotion.com', 'Dailymotion'],
        ];

        // boucle sur les noms
        foreach ($profiles as $profile) {
            $newprofile = new Profile();
            $newprofile->setUrl($profile[0]);
            $newprofile->setRs($profile[1]);
            $manager->persist($newprofile);
        }

        // exécute les requêtes
        $manager->flush();
    }
}
