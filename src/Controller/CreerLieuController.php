<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CreerLieuController extends AbstractController
{

    /**
     * @Route("/creer/ville", name="creer_ville")
     */
    public function creerVille(EntityManagerInterface $em, Request $request)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");
        $ville = new Ville();
        $villeForm = $this->createForm(VilleType::class, $ville) ;

        $villeForm->handleRequest($request);
        if ($villeForm->isSubmitted() && $villeForm->isValid())
        {
            $em->persist($ville);
            $em->flush();

            $this->addFlash("success", "Ville ajoutée !");
            return $this->redirectToRoute("creer_lieu");
        }
        return $this->render('creer_lieu/creerville.html.twig', [
            "villeForm"=>$villeForm->createView(),
        ]);
    }
    /**
     * @Route("/creer/lieu", name="creer_lieu")
     */
    public function creerLieu(EntityManagerInterface $em, Request $request)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");
        $donnees = $this->getRequest()->get("donnees");
        return $this->render('creer_lieu/creerlieu.html.twig');
    }
}
