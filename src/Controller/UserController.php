<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ProfilType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/user/{id}", name="monprofil", requirements={"id": "\d+"})
     */
    /*Cette fonction renvoie la page qui permet dafficher le profil de lutilisateur connecté afin quil le modifie*/
    public function afficherEtUpdateMonProfil($id, Request $request)
    {
        $id = 1;
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository(Participant::class);
        $user = $userRepo->findBy(['id'=>'1']);
        $profilForm = $this->createForm(ProfilType::class);
        $profilForm->handleRequest($request);
        return $this->render('user/myprofil.html.twig', [
            "profilForm"=>$profilForm->createView(),
            "user"=>$user
        ]);
    }
}
