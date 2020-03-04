<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\ModifierProfilType;
use App\Form\ModifierSortieType;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ModifierSortieController extends AbstractController
{
    /**
     * @Route("/modifier/sortie/{id}", name="modifier_sortie", requirements={"id":"\d+"})
     */
    public function modifierSortie($id, Request $request, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");
        $em = $this->getDoctrine()->getManager();
        $sortie = $em->getRepository(Sortie::class)->find($id);
        $updateForm = $this->createForm(ModifierSortieType::class, $sortie);
        $sortie->getOrganise();
        $updateForm->handleRequest($request);

        return $this->render('sortie/modifiersortie.html.twig', [
        "updateForm"=>$updateForm->createView(),
            "sortie"=>$sortie
        ]);
    }
}
