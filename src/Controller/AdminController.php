<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/creerCompte", name="creerCompte")
     */
    public function creerCompte(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $basicUser = new Participant();
        $basicUser->setActif(1);
        $basicUser->setAdministrateur(1);
        $registerForm = $this->createForm(RegisterType::class, $basicUser);

        $registerForm->handleRequest($request);
        if($registerForm->isSubmitted() && $registerForm->isValid())
        {
            $hashed = $encoder->encodePassword($basicUser, $basicUser->getPassword());
            $basicUser->setMotPasse($hashed);

            $em->persist($basicUser);
            $em->flush();
            $this->addFlash("success", "Compte créé.");
            return $this->redirectToRoute("home_home");
        }
        return $this->render('admin/register.html.twig',
            [
                "registerForm"=> $registerForm->createView(),
            ]);
    }
}
