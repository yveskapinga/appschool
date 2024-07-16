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

        // Convertissez la date en format Y-m-d (année-mois-jour)
        $startOfMonth = new \DateTime($annee.'-'.$mois.'-01');
        $endOfMonth = clone $startOfMonth;
        $endOfMonth->modify('+1 month');

        return $qb
            ->innerJoin('p.eleve', 'e')
            ->where('p.createdAt >= :start_date')
            ->andWhere('p.createdAt < :end_date')
            ->andWhere('e.classe = :classe')
            ->setParameter('start_date', $startOfMonth)
            ->setParameter('end_date', $endOfMonth)
            ->setParameter('classe', $classe)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // src/Repository/PaiementRepository.php

    public function findDistinctMonthsAndYears()
    {
        $qb = $this->createQueryBuilder('p')
            ->select('DISTINCT YEAR(p.createdAt) as year, MONTH(p.createdAt) as month')
            ->orderBy('year', 'DESC')
            ->addOrderBy('month', 'DESC');

        return $qb->getQuery()->getResult();
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
