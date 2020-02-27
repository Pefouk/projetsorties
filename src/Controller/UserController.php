<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\ModifierProfilType;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/user/monProfil/{id}", name="monprofil", requirements={"id": "\d+"})
     */
    /*Cette fonction renvoie la page qui permet d'afficher le profil de l'utilisateur connecté afin qu'il le modifie*/
    public function afficherMonProfil($id, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $profil = $entityManager->getRepository(Participant::class)->find($id);
        $profilForm = $this->createForm(ProfilType::class, $profil);
        return $this->render('user/myprofil.html.twig', [
            "profilForm" => $profilForm->createView(),
            "profil" => $profil
        ]);
    }

    /**
     * @Route("/user/modifierProfil/{id}", name="modifierProfil", requirements={"id": "\d+"})
     */
    /*Cette fonction permet à l'utilisateur de modifier son profil*/
    public function modifierProfil($id, Request $request)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $em = $this->getDoctrine()->getManager();
        $profil = $em->getRepository(Participant::class)->find($id);
        $updateForm = $this->createForm(ModifierProfilType::class, $profil);
        $updateForm->handleRequest($request);

        if ($updateForm->isSubmitted() && $updateForm->isValid()) {
            $em->persist($profil);
            $em->flush();

            $this->addFlash("success", "Votre profil a bien été modifié !");
            return $this->redirectToRoute('monprofil', ['id' => $profil->getId()]);
        }
        return $this->render('user/modifier.html.twig', [
            "updateForm" => $updateForm->createView(),
            "profil" => $profil
        ]);
    }

    /**
     * @Route("/sinscrire/{id}",name="sinscrire")
     */
    public function sinscrire($id, Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $sortieRepo = $em->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);

        $user->addSortie($sortie);

        $em->persist($user);
        $em->flush();
        $this->addFlash('success', 'Félicitation ! vous etes inscrit à la sortie : ' . $sortie->getNom() . '!');
        return $this->redirectToRoute("home_home");
    }

    /**
     * @Route("/desinscrire/{id}",name="desinscrire")
     */
    public function desinscrire($id, Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $sortieRepo = $em->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);

        $user->remove($sortie);

        $em->persist($user);
        $em->flush();
        $this->addFlash('success', 'Félicitation ! vous etes inscrit à la sortie : ' . $sortie->getNom() . '!');
        return $this->redirectToRoute("home_home");
    }
}
