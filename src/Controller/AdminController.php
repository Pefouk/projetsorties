<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AdminController
 * @package App\Controller
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{

    /**
     * @Route("/ListeUtilisateurs",name="ListeUtilisateurs")
     */

    public function listerUtilisateurs(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $utilisateursRepo = $em->getRepository(Participant::class);
        $liste = $utilisateursRepo->findAll();
        return $this->render('admin/listeUtilisateurs.html.twig', [
            "liste" => $liste,
            "profil" => $user
        ]);
    }

    /**
     * @Route("/desactiver/{id}",name="desactiver")
     */

    public function desactiver(Request $request, EntityManagerInterface $em, $id)
    {

        $entity = $em->getRepository(Participant::class)->find($id);

        if ($entity != null) {
            $entity->setActif(0);
            $em->flush();
            return $this->redirectToRoute('admin_ListeUtilisateurs');
        }
    }

    /**
     * @Route("/activer/{id}",name="activer")
     */

    public function activer(Request $request, EntityManagerInterface $em, $id)
    {

        $entity = $em->getRepository(Participant::class)->find($id);

        if ($entity != null) {
            $entity->setActif(1);
            $em->flush();
            return $this->redirectToRoute('admin_ListeUtilisateurs');
        }
    }

    /**
     * @Route("/supprimer/{id}",name="supprimer")
     */
    public function supprimer(Request $request, EntityManagerInterface $em, $id)
    {


        $entity = $em->getRepository(Participant::class)->find($id);
        $listSortie = $entity->getParticipe();

        foreach ($listSortie as $uneSortie){
        if($entity->isInscrit($uneSortie))
        $uneSortie->removeInscrit($entity);
        }


        if ($entity != null) {
            $em->remove($entity);
            $em->flush();

            return $this->redirectToRoute('admin_ListeUtilisateurs');
        }
    }

    /**
     * @Route("/creerCompte", name="creerCompte")
     */
    public function creerCompte(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $basicUser = new Participant();
        $basicUser->setActif(1);
        $basicUser->setAdministrateur(0);
        $registerForm = $this->createForm(RegisterType::class, $basicUser);
        $registerForm->handleRequest($request);
        if ($registerForm->isSubmitted() && $registerForm->isValid()) {
            $hashed = $encoder->encodePassword($basicUser, $basicUser->getPassword());
            $basicUser->setMotPasse($hashed);

            $em->persist($basicUser);
            $em->flush();
            $this->addFlash("success", "Compte créé.");
            return $this->redirectToRoute("home_home");
        }
        return $this->render('admin/register.html.twig',
            [
                "registerForm" => $registerForm->createView(),
            ]);
    }
}
