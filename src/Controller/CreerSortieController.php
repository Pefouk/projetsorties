<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Etat;
use App\Entity\Ville;
use App\Form\CreerSortieType;
use App\Form\LieuType;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $newlieu = new Lieu();
        $newVille = new Ville();
        $lieu = $em->getRepository(Lieu::class);
        $sortie->getLieu($lieu);
        $etatRep = $this->getDoctrine()->getRepository(Etat::class);

        $organisateur = $this->getUser();
        $sortie->setOrganise($organisateur);
        $sortie->setCampus($organisateur->getCampus());
        $lieuRepo = $this->getDoctrine()->getRepository(Lieu::class);
        $lieux = $lieuRepo->findBy([]);

        $sortieForm = $this->createForm(CreerSortieType::class, $sortie);
        $lieuForm = $this->createForm(LieuType::class, $newlieu);
        $villeForm = $this->createForm(VilleType::class, $newVille);

        $sortieForm->handleRequest($request);

        if($organisateur instanceof Participant)
        {
            if($sortieForm->isSubmitted() && $sortieForm->isValid()) {
                if ($sortieForm->getClickedButton() === $sortieForm->get('enregistrer')) {
                    $etat = $etatRep->find(1);
                    $sortie->setEtat($etat);

                    $em->persist($sortie);
                    $em->flush();
                    $this->addFlash("success", "Votre sortie a bien été créée !");
                    return $this->redirectToRoute('sorties_afficher');
                }
                if ($sortieForm->getClickedButton() === $sortieForm->get('publier')) {
                    $etat = $etatRep->find(2);
                    $sortie->setEtat($etat);

                    $em->persist($sortie);
                    $em->flush();
                    $this->addFlash("success", "Votre sortie a bien été publiée !");
                    return $this->redirectToRoute('sorties_afficher');
                }
            }

            $lieuForm->handleRequest($request);
            $villeForm->handleRequest($request);
        }

        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            $em->persist($newlieu);
            $em->flush();

            $this->addFlash("success", "Votre lieu a bien été ajouté à la liste !");
            return $this->redirectToRoute('creer_sortie');
        }

        if($villeForm->isSubmitted())
        {
            $em->persist($newVille);
            $em->flush();

            $this->addFlash("success","Ville ajoutée !");
        }
        return $this->render('sortie/creersortie.html.twig', [
            "sortieForm"=>$sortieForm->createView(),
            "lieuForm"=>$lieuForm->createView(),
            "villeForm"=>$villeForm->createView(),
            "lieux"=> $lieux,
        ]);
    }

    /**
     * @Route("/creer/publier/sortie", name="publiermtnsortie")
     */

    public function publierSortie(Request $request, EntityManagerInterface $entityManager)
    {


    }



}
