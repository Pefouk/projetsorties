<?php

namespace App\Repository;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormInterface;

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

    public function findByCampusAndNom(FormInterface $form, Participant $participant)
    {
        $campus = $form->getData()['campus'];
        if ($form->getData()['recherche'] === null)
            $nom = '%';
        else
            $nom = '%' . $form->getData()['recherche'] . '%';
        if ($form->getData()['datemin'] instanceof \DateTime) {
            $datemin = $form->getData()['datemin'];
            $datemax = $form->getData()['datemax'];
        } else {
            $datemax = false;
            $datemin = false;
        }
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->addSelect('e')
            ->addSelect('o')
            ->addSelect('c')
            ->addSelect('l')
            ->addSelect('i')
            ->innerJoin('s.organise', 'o')
            ->innerJoin('s.etat', 'e')
            ->innerJoin('s.campus', 'c')
            ->innerJoin('s.lieu', 'l')
            ->leftJoin('s.inscrit', 'i')
            ->where('s.campus = :campus')
            ->andWhere('s.nom LIKE :nom');
        if ($datemin !== false && $datemax !== false)
            $query->andWhere('s.dateHeureDebut > :datemin AND s.dateHeureDebut < :datemax')
                ->setParameter('datemin', $datemin)
                ->setParameter('datemax', $datemax);
        $query->setParameter('nom', $nom)
            ->setParameter('campus', $campus);
        return $query->getQuery()->getResult();
    }

    public function findByCampus(Campus $campus)
    {
        return $this->createQueryBuilder('s')
            ->select('s')
            ->addSelect('e')
            ->addSelect('o')
            ->addSelect('c')
            ->addSelect('l')
            ->addSelect('i')
            ->innerJoin('s.organise', 'o')
            ->innerJoin('s.etat', 'e')
            ->innerJoin('s.campus', 'c')
            ->innerJoin('s.lieu', 'l')
            ->leftJoin('s.inscrit', 'i')
            ->where('s.campus = :campus')
            ->setParameters(['campus' => $campus])
            ->getQuery()
            ->getResult();
    }
    /*
     * $this->createQueryBuilder('s')->
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
            ->setParameters(['nom' => $nom, 'campus' => $campus, 'datemin' => $datemin, 'datemax' => $datemax]);
     */
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
