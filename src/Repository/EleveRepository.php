<?php

namespace App\Repository;

use App\Entity\Eleve;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Eleve>
 */
class EleveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Eleve::class);
    }

    public function rechercherEleve(string $critere): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.nom LIKE :critere OR e.prenom LIKE :critere')
            ->setParameter('critere', '%' . $critere . '%')
            ->getQuery()
            ->getResult();

        
    }

    public function trouverInscriptionsEtReinscriptions(): array
    {
        // Crée un QueryBuilder pour l'entité Eleve
        return $this->createQueryBuilder('e')
            // Sélectionne tous les champs de l'entité Eleve et calcule la date de la dernière inscription ou réinscription
            // La fonction GREATEST est utilisée pour obtenir la plus grande valeur entre createdAt et updatedAt
            // HIDDEN indique à Doctrine de calculer la valeur last_inscription mais de ne pas l'inclure dans les résultats retournés
            ->select('e, GREATEST(e.createdAt, COALESCE(e.updatedAt, e.createdAt)) AS HIDDEN last_inscription')
            // Trie les élèves par la date de la dernière inscription ou réinscription en ordre décroissant
            ->orderBy('last_inscription', 'DESC')
            // Exécute la requête et retourne les résultats
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return Eleve[] Returns an array of Eleve objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Eleve
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
