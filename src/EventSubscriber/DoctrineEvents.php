<?php


namespace App\EventSubscriber;


use App\Entity\Etat;
use App\Entity\Sortie;
use DateTime;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class DoctrineEvents implements EventSubscriber
{

    private $etat;

    public function __construct()
    {
        $this->etat = null;
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postLoad,
            Events::postPersist
        ];
    }

    public function postPersist(LifecycleEventArgs $lifecycleEventArgs)
    {
        $entity = $lifecycleEventArgs->getEntity();

        dump($entity);
    }
        public function postLoad(LifecycleEventArgs $lifecycleEventArgs)
    {
        $entity = $lifecycleEventArgs->getEntity();

        if ($this->etat === null) {
            $this->etat = $lifecycleEventArgs->getEntityManager()->getRepository('App:Etat')->findAll();
        }
        if ($entity instanceof Sortie) {
            $this->updateEtat($entity, $lifecycleEventArgs->getEntityManager());
        }
    }

    private function updateEtat(Sortie $sortie, EntityManager $entityManager)
    {
        $ajd = new DateTime();

        if ($sortie->getEtat()->getId() !== 6) {
            if ($sortie->getEtat()->getId() === 2 && ($sortie->getNbInscriptionMax() === count($sortie->getInscrit()) || $sortie->getDateLimiteInscription() < $ajd)) {
                $sortie->setEtat($this->etat[2]);
            }
            else if ($sortie->getEtat()->getId() === 3 && $sortie->getNbInscriptionMax() > count($sortie->getInscrit()) && $sortie->getDateLimiteInscription() >= $ajd) {
                $sortie->setEtat($this->etat[1]);
            }
            if ($sortie->getDateHeureDebut() <= $ajd && $sortie->getDateHeureDebut()->getTimestamp() + $sortie->getDuree()->getTimestamp() >= $ajd->getTimestamp())
                $sortie->setEtat($this->etat[3]);
            else if ($sortie->getDateHeureDebut()->getTimestamp() + $sortie->getDuree()->getTimestamp() <= $ajd->getTimestamp())
                $sortie->setEtat($this->etat[4]);
            try {
                $entityManager->persist($sortie);
                $entityManager->flush($sortie);
            } catch (OptimisticLockException $e) {
            } catch (ORMException $e) {
            }
        }
    }
}