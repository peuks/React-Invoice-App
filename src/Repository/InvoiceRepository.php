<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Invoice;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }
    /**
     * Selectionner la table invoice à laquelle on joint
     * l'utilisateur
     * @param User $user
     * @return void
     */
    public function findNextChrono(User $user): int
    {
        return $this->createQueryBuilder("invoice")
            // Selectionner le chrono de l'invoice 
            ->select("invoice.chrono")
            // On veut trouver le customer liée à la facture
            ->join("invoice.customer", "customer")
            // ou l'utilisateur est l'utilisateur renvoyé
            ->where("customer.user = :user")
            ->setParameter("user", $user)
            // Ordonner du plus grand au plus petit
            ->orderBy("invoice.chrono", "DESC")
            // Récupérer un seul résultat
            ->setMaxResults(1)

            ->getQuery()
            // Récupérer uniquement le numéro et l'incrémenter
            ->getSingleScalarResult() + 1;
    }
    // /**
    //  * @return Invoice[] Returns an array of Invoice objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Invoice
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
