<?php

namespace App\DataFixtures;

use App\Entity\Personne;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PersonneMixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 100; $i++) {
            // create personne
            $personne = new Personne();
            $personne->setFirstname($faker->firstName);
            $personne->setName($faker->lastName);
            $personne->setAge($faker->numberBetween(18, 65));
            // persist dans transaction
            $manager->persist($personne);
        }
        // commit transaction
        $manager->flush();
    }
}
