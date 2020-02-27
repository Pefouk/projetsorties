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
        $unmois = new \DateTime();
        $ajd = new \DateTime();
        $unmois->sub(date_interval_create_from_date_string('1 month'));
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
            ->andWhere('s.nom LIKE :nom')
            ->andWhere('s.dateHeureDebut > :unmois');
        if ($form->getData()['organise'])
            $query->andWhere('s.organise = :user');
        if ($form->getData()['passee'])
            $query->andWhere('s.dateHeureDebut < :ajd');
        if ($datemin !== false && $datemax !== false)
            $query->andWhere('s.dateHeureDebut > :datemin AND s.dateHeureDebut < :datemax')
                ->setParameter('datemin', $datemin)
                ->setParameter('datemax', $datemax);
        $query->setParameter('nom', $nom)
            ->setParameter('campus', $campus)
            ->setParameter('unmois', $unmois);
        if ($form->getData()['organise'])
            $query->setParameter('user', $participant);
        if ($form->getData()['passee'])
            $query->setParameter('ajd', $ajd);
        return $query->getQuery()->getResult();
    }

    /*
     *   "recherche" => null
     *   "datemin" => null
     *   "datemax" => null
     *   "organise" => false
     *   "inscrit" => false
     *   "nonInscrit" => false
     *   "passee" => false
     */

    public function findByCampus(Campus $campus)
    {
        $unmois = new \DateTime();

        $unmois->sub(date_interval_create_from_date_string('1 month'));
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
            ->andWhere('s.dateHeureDebut > :unmois')
            ->setParameters(['campus' => $campus, 'unmois' => $unmois])
            ->getQuery()
            ->getResult();
    }


    public function findbyId(int $id)
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
            ->where('s.id = :id')
            ->setParameters(['id' => $id])
            ->getQuery()
            ->getResult()[0];
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
