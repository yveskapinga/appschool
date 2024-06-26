<?php

namespace App\DataFixtures;

use App\Factory\ClasseFactory;
use App\Factory\EleveFactory;
use App\Factory\EnseignantFactory;
use App\Factory\MatiereFactory;
use App\Factory\PaiementFactory;
use App\Factory\ParentsFactory;
use App\Factory\PersonnelFactory;
use App\Factory\UtilisateurFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // UtilisateurFactory::createMany(1);
        // ClasseFactory::createMany(6);
        // MatiereFactory::createMany(10);
        // PersonnelFactory::createMany(10);
        // ParentsFactory::createMany(10);
        // PaiementFactory::createMany(8);
        EleveFactory::createMany(10);
        // EnseignantFactory::createMany(1);
        $manager->flush();
    }
}
