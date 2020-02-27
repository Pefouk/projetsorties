<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
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
        $sortie->setLieu($lieu);
        $sortieForm = $this->createForm(CreerSortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid())
        {
            $em->persist($sortie);
            $em->flush();

            $this->addFlash("success", "Votre sortie a bien été créée.");

        }
        return $this->render('creer_sortie/creersortie.html.twig', [
            "sortieForm"=>$sortieForm->createView(),
        ]);
    }
}
