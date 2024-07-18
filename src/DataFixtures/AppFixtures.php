<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Classe;
use App\Entity\Eleve;
use App\Entity\Enseignant;
use App\Entity\Matiere;
use App\Entity\Parents;
use App\Entity\Personnel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    // Je viens de faire un push sur github
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private ManagerRegistry $managerRegistry
    ) {
    }
    private $nom = [
        'KALEMI',
        'KATANGA',
        'INGETA',
        'ZOLA',
        'MWAPE',
        'KASEYA',
        'SAINI',
        'SILAS',
        'ZAKUMI',
        'MUNDUYU',
        'YULUBA',
        'ZAMINI',
        'MUKONKO',
        'KALUME',
        'KITENGE',
        'KANTENG',
        'KASIG',
        'KASONGO',
        'KASONJI',
        'MALINI',
        'MILINDI',
        'NZITA',
        'KISITA',
        'KAMONA',
        'KISIMBA',
        'MWANANGWA',
    ];

    private $matiere = [
        'Mathématique',
        'Francais',
        'Histoire',
        'Anglais',
        'Religion',
        'Caligraphie',
        'Expression orale',
        'Expression écrite',
        'Vocabulaire',
        'Mesures de grandeurs',
        'Formes géométriques',
        'Numération',
        'Opérations',
        'Problèmes',
        "Sciences d'éveil",
        'Technologie',
        'Education civique',
        'Environnement',
        'Education physique et sports',
    ];

    public function load(ObjectManager $manager): void
    {
        $em = $this->managerRegistry->getManager();

        // Ajouter des élèves dans une classe

        $this->bindEnseignantClasse();
        $this->bindEleveClasse();
        // $this->bindEleveClasse();

        // Création des élèves
        for ($i = 0; $i < 100; ++$i) {
            $eleve = new Eleve();
            $manager->persist($this->createEleve($eleve));
        }

        // Création des classes
        $this->createClasse(6, $manager);

        // Création des enseignants
        for ($i = 0; $i < 30; ++$i) {
            $enseignant = new Enseignant();
            $manager->persist($this->createEnseignant($enseignant));
        }

        // Création de l'administrateur
        $manager->persist($this->createAdmin(new Admin()));

        // Création des personnels
        for ($i = 0; $i < 10; ++$i) {
            $personnel = new Personnel();
            $manager->persist($this->createPersonnel($personnel));
        }

        // Création des matières
        $this->createMatiere($manager);

        // Créations des parents
        for ($i = 0; $i < 100; ++$i) {
            $parents = new Parents();
            $manager->persist($this->createParents($parents));
        }

        // Associer les élèves aux parents
        for ($i = 0; $i < 100; ++$i) {
            $parent = $em->getRepository(Parents::class)->find($i);
            $eleve = $em->getRepository(Eleve::class)->find($i);
            $parent->addElefe($eleve);
            $manager->persist($eleve);
        }

        $manager->flush();
    }

    private function bindEnseignantClasse()
    {
        $em = $this->managerRegistry->getManager();
        $classes = $em->getRepository(Classe::class)->findAll();
        $profRepo = $this->managerRegistry->getRepository(Enseignant::class);

        $incrementor = 1;
        foreach ($classes as $classroom) {
            $enseignant = $profRepo->find($incrementor + 5);

            // Vérifiez que l'objet retourné est bien un Enseignant
            if (!$enseignant instanceof Enseignant) {
                throw new \Exception('L\'objet retourné n\'est pas un Enseignant');
            }

            $classroom->addEnseignant($enseignant);
            ++$incrementor;

            $em->persist($classroom);
        }

        $em->flush();
    }

    private function bindEleveClasse()
    {
        $em = $this->managerRegistry->getManager();
        $classes = $em->getRepository(Classe::class)->findAll();
        $incrementor = 1;
        foreach ($classes as $classroom) {
            for ($i = $incrementor; $i < ($incrementor + 5); ++$i) {
                $eleve = $em->getRepository(Eleve::class)->find($i);
                $classroom->addEleve($eleve);
            }
            $incrementor = $i;
        }
    }

    private function bindEnseignantMatiere()
    {
        $em = $this->managerRegistry->getManager();
        $matiere = $em->getRepository(Matiere::class)->findAll();
        $enseignants = $em->getRepository(Enseignant::class)->findAll();
        $totalEnseignant = count($enseignants);
        $enseignantCounter = 0;

        foreach ($matiere as $cours) {
            $cours
            ->addEnseignant($enseignants[$totalEnseignant - $enseignantCounter])
            ->setProfesseurPrincipal($enseignants[$enseignantCounter]);
            $em->persist($cours);
            ++$enseignantCounter;
        }
    }

    private function myFaker()
    {
        return Factory::create('fr_FR');
    }

    public function createEnseignant(Enseignant $enseignant): Enseignant
    {
        return $enseignant
        ->setAdresse($this->myFaker()->address())
        ->setDateDeNaissance($this->myFaker()->dateTimeBetween($startDate = '1960-01-01', $endDate = '2000-12-31'))
        ->setEmail($this->myFaker()->email())
        ->setFonction('Enseignant')
        ->setMatricule($this->myFaker()->unique()->randomNumber($nbDigits = 5))
        ->setNom($this->nom[rand(0, 23)])
        ->setNumeroTelephone('0813197098')
        ->setPassword($this->hasher->hashPassword($enseignant, 'enseignant1'))
        ->setPhoto('url_photo')
        ->setPrenom($this->myFaker()->firstName())
        ->setPostNom($this->myFaker()->randomElement($this->nom))
        ->setRoles(['ROLE_ENSEIGNANT'])
        ->setSexe($this->myFaker()->randomElement(['M', 'F']))
        ->setVerified(true);
    }

    public function createPersonnel(Personnel $personnel): Personnel
    {
        return $personnel
        ->setAdresse($this->myFaker()->address())
        ->setDateDeNaissance($this->myFaker()->dateTimeBetween($startDate = '1970-01-01', $endDate = '1990-12-31'))
        ->setEmail($this->myFaker()->email())
        ->setFonction('Personnel')
        ->setMatricule($this->myFaker()->unique()->randomNumber($nbDigits = 5))
        ->setNom($this->nom[rand(0, 23)])
        ->setNumeroTelephone('0813197098')
        ->setPassword($this->hasher->hashPassword($personnel, 'personnel1'))
        ->setPhoto('url_photo')
        ->setPrenom($this->myFaker()->firstName())
        ->setPostNom($this->myFaker()->randomElement($this->nom))
        ->setRoles(['ROLE_ADMIN'])
        ->setSexe($this->myFaker()->randomElement(['M', 'F']))
        ->setVerified(true);
    }

    public function createEleve(Eleve $eleve): Eleve
    {
        return $eleve
        ->setAdresse($this->myFaker()->address())
        ->setDateDeNaissance($this->myFaker()->dateTimeBetween($startDate = '2017-01-01', $endDate = '2018-12-31'))
        ->setEmail($this->myFaker()->email())
        ->setIdentificationNationale($this->myFaker()->unique()->randomNumber($nbDigits = 9))
        ->setNom($this->nom[rand(0, 23)])
        ->setNumeroTelephone('0813197098')
        ->setPassword($this->hasher->hashPassword($eleve, 'eleve1'))
        ->setPhoto('url_photo')
        ->setPrenom($this->myFaker()->firstName())
        ->setPostNom($this->myFaker()->randomElement($this->nom))
        ->setRoles(['ROLE_ELEVE'])
        ->setSexe($this->myFaker()->randomElement(['M', 'F']))
        ->setVerified(true);
    }

    public function createParents(Parents $parents): Parents
    {
        return $parents
        ->setAdresse($this->myFaker()->address())
        ->setDateDeNaissance($this->myFaker()->dateTimeBetween($startDate = '1960-01-01', $endDate = '1990-12-31'))
        ->setEmail($this->myFaker()->email())
        ->setNom($this->nom[rand(0, 23)])
        ->setNumeroTelephone('0998615075')
        ->setPassword($this->hasher->hashPassword($parents, 'parents1'))
        ->setPhoto('url_photo')
        ->setPrenom($this->myFaker()->firstName())
        ->setPostNom($this->myFaker()->randomElement($this->nom))
        ->setRoles(['ROLE_PARENTS'])
        ->setSexe($this->myFaker()->randomElement(['M', 'F']))
        ->setVerified(true);
    }

    public function createMatiere(ObjectManager $manager): void
    {
        foreach ($this->matiere as $cours) {
            $matiere = new Matiere();
            $matiere
               ->setCoefficient(rand(2, 5))
               ->setDesignation($cours);
            $manager->persist($matiere);
        }
    }

    public function createAdmin(Admin $admin): Admin
    {
        return $admin
        ->setAdresse($this->myFaker()->address())
        ->setDateDeNaissance($this->myFaker()->dateTimeBetween($startDate = '1975-01-01', $endDate = '2000-12-31'))
        ->setEmail($this->myFaker()->email())
        ->setNom($this->nom[rand(0, 23)])
        ->setNumeroTelephone('0813197098')
        ->setPassword($this->hasher->hashPassword($admin, 'admin'))
        ->setPhoto('url_photo')
        ->setPrenom($this->myFaker()->firstName())
        ->setPostNom($this->myFaker()->randomElement($this->nom))
        ->setRoles(['ROLE_ADMIN'])
        ->setSexe($this->myFaker()->randomElement(['M', 'F']))
        ->setVerified(true);
    }

    public function createClasse($id, ObjectManager $manager): void
    {
        $designation = ['A', 'B', 'C'];
        for ($i = 1; $i <= $id; ++$i) {
            foreach ($designation as $classroom) {
                $classe = new Classe();
                $classe->setclasse($i)
                ->setDesignation($classroom)
                ->setSection('primaire')
                ->setProfesseurPrincipal($manager->getRepository(Classe::class)->find($i));
                $manager->persist($classe);
            }
        }

        for ($i = 1; $i < 4; ++$i) {
            foreach ($designation as $classroom) {
                $classe = new Classe();
                $classe->setclasse($i)
                ->setDesignation($classroom)
                ->setSection('maternelle');
                $manager->persist($classe);
            }
        }
    }
}
