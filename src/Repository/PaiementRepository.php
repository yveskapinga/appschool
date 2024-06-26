<?php

namespace App\Repository;

use App\Entity\Classe;
use App\Entity\Eleve;
use App\Entity\Paiement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Paiement>
 */
class PaiementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paiement::class);
    }

    public function findByEleve(Eleve $eleve)
    {
        return $this->createQueryBuilder('p')
            ->where('p.eleve = :eleve')
            ->setParameter('eleve', $eleve)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByMonthAndClasse(int $mois, int $annee, Classe $classe)
    {
        $qb = $this->createQueryBuilder('p');

        return $qb
            ->innerJoin('p.eleve', 'e')
            ->where('MONTH(p.createdAt) = :mois')
            ->andWhere('YEAR(p.createdAt) = :annee')
            ->andWhere('e.classe = :classe')
            ->setParameter('mois', $mois)
            ->setParameter('annee', $annee)
            ->setParameter('classe', $classe)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Paiement[] Returns an array of Paiement objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Paiement
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
