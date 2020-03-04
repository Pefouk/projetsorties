<?php


namespace App\EventSubscriber;


use App\Entity\Etat;
use App\Entity\Sortie;
use App\Exception\SortieException;
use DateTime;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Config\Definition\Exception\Exception;

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
            Events::prePersist
        ];
    }

    public function prePersist(LifecycleEventArgs $lifecycleEventArgs)
    {
        $entity = $lifecycleEventArgs->getEntity();

        if ($entity instanceof Sortie) {
            $this->checkSortie($entity);
        }
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

    private function checkSortie(Sortie $entity)
    {
        $unJour = new \DateTime();
        $unJour->add(date_interval_create_from_date_string('1 day'));
        if ($entity->getNbInscriptionMax() <= 1)
            throw new SortieException('Nombre d\'inscrit minimal incorrect, merci de demander minimum 2 personnes !');
        if ($entity->getDateLimiteInscription() < $unJour)
            throw new SortieException('Date limite d\'inscription incorrect, merci de prevoir un jour minimum !');
        if ($entity->getDateLimiteInscription() > $entity->getDateHeureDebut())
            throw new SortieException('Date invalide, merci de cloturer les inscriptions APRES le début de la sortie !');
        if ($entity->getDuree()->getTimestamp() + $entity->getDuree()->getOffset() < 60 * 10)
            throw new SortieException('Durée invalide, merci de prévoir au minimum 10 minutes !');
        if ($entity->getDuree()->getTimestamp() + $entity->getDuree()->getOffset() > 60 * 60 * 24)
            throw new SortieException('Durée invalide, merci de prévoir moins d\'un jour !');
        if (strlen($entity->getNom()) <= 2)
            throw new SortieException('Merci de mettre un nom de plus de 2 caractères !');
        $unJour->add(date_interval_create_from_date_string('1 day'));
        if ($entity->getDateHeureDebut() <= $unJour)
            throw new SortieException('Date invalide, merci de prévoir au moins 2 jour pour le début de la sortie !');
        if (strlen($entity->getInfosSortie()) < 20)
            throw new SortieException('Description invalide, merci d\'écrire au moins 20 caractères !');
    }
}