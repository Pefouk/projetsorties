<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findByCampusNomAndDate($campus, $nom, $datemin, $datemax)
    {
        return $this->createQueryBuilder('s')
            ->select('s')
            ->addSelect('e')
            ->addSelect('o')
            ->addSelect('c')
            ->innerJoin('s.organisateur', 'o')
            ->innerJoin('s.etat', 'e')
            ->innerJoin('s.campus', 'c')
            ->where('s.etat = e.id')
            ->andWhere('s.organisateur = o.id')
            ->andWhere('s.campus = :campus')
            ->andWhere('s.nom LIKE :nom')
            ->andWhere('s.dateHeureDebut > :datemin AND s.dateHeureDebut < :datemax')
            ->setParameters(['nom' => $nom, 'campus' => $campus, 'datemin' => $datemin, 'datemax' => $datemax])
            ->getQuery()
            ->getResult();
    }

    public function findByCampusAndNom($campus, $nom)
    {
        return $this->createQueryBuilder('s')
        ->select('s')
        ->addSelect('e')
        ->addSelect('o')
        ->addSelect('c')
        ->innerJoin('s.organisateur', 'o')
        ->innerJoin('s.etat', 'e')
        ->innerJoin('s.campus', 'c')
        ->where('s.etat = e.id')
        ->andWhere('s.organisateur = o.id')
        ->andWhere('s.campus = :campus')
        ->andWhere('s.nom LIKE :nom')
        ->setParameters(['nom' => $nom, 'campus' => $campus])
        ->getQuery()
        ->getResult();
    }
    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
