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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
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
    public function modifierProfil($id, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $em = $this->getDoctrine()->getManager();
        $profil = $em->getRepository(Participant::class)->find($id);
        $updateForm = $this->createForm(ModifierProfilType::class, $profil);
        $updateForm->handleRequest($request);

//        if ($updateForm->isValid()) {
//            dd('1');
//        } else {
//            dd($updateForm->getErrors(true));
//        }
        if ($updateForm->isSubmitted() && $updateForm->isValid()) {
//            $hashed = $encoder->encodePassword($profil, $profil->getPassword());
//            $profil->setMotPasse($hashed);

            $em->persist($profil);
            $em->flush();
            $profil->setImageFile(null);

            $this->addFlash("success", "Votre profil a bien été modifié !");
            return $this->redirectToRoute('monprofil', ['id' => $profil->getId()]);
        }
        return $this->render('user/modifier.html.twig', [
            "updateForm" => $updateForm->createView(),
        ]);
    }

    /**
     * @Route("/sinscrire/{id}",name="sinscrire")
     */
    public function sinscrire($id, Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $sortieRepo = $em->getRepository(Sortie::class);
        $sortie = $sortieRepo->findbyId($id);

        $user->addParticipe($sortie);

        $em->persist($user);
        $em->flush();
        $this->addFlash('success', 'vous etes inscrit à la sortie : ' . $sortie->getNom() . '!');
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/desinscrire/{id}",name="desinscrire")
     */
    public function desinscrire($id, Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $sortieRepo = $em->getRepository(Sortie::class);
        $sortie = $sortieRepo->findbyId($id);

        $user->removeParticipe($sortie);

        $em->persist($user);
        $em->flush();
        $this->addFlash('danger', 'Enfoiré tu t\'es désisté à : ' . $sortie->getNom() . '!');
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
}
