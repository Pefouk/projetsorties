<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Etat;
use App\Form\CreerSortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CreerSortieController extends AbstractController
{
    /**
     * @Route("/creer/sortie", name="creer_sortie")
     */
    public function creerSortie(EntityManagerInterface $em, Request $request)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $sortie = new Sortie();
        $lieu = $em->getRepository(Lieu::class);
        $sortie->getLieu($lieu);
        $etatRep = $this->getDoctrine()->getRepository(Etat::class);
        $etat = $etatRep->find(1);
        $sortie->setEtat($etat);
        $organisateur = $this->getUser();
        if($organisateur instanceof Participant)
        {
            $sortie->setOrganise($organisateur);
            $sortie->setCampus($organisateur->getCampus());
            $lieuRepo = $this->getDoctrine()->getRepository(Lieu::class);
            $lieux = $lieuRepo->findBy([]);
            $sortieForm = $this->createForm(CreerSortieType::class, $sortie);

            $sortieForm->handleRequest($request);
        }


        if($sortieForm->isSubmitted() && $sortieForm->isValid())
        {
            $em->persist($sortie);
            $em->flush();

            $this->addFlash("success", "Votre sortie a bien été créée.");

        }
        return $this->render('creer_sortie/creersortie.html.twig', [
            "sortieForm"=>$sortieForm->createView(),
            "lieux"=> $lieux,
        ]);
    }
}
