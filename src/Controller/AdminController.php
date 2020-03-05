<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
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
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param $id
     * @return RedirectResponse
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
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param $id
     * @return RedirectResponse
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
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param $id
     * @return RedirectResponse
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
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     * @return RedirectResponse|Response
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
            return $this->redirectToRoute("admin_ListeUtilisateurs");
        }
        return $this->render('admin/register.html.twig',
            [
                "registerForm" => $registerForm->createView(),
            ]);
    }

    /**
     * @Route("/passerAdmin/{id}",name="passerAdmin")
     */
    public function passerAdmin($id,Request $request, EntityManagerInterface $em){
        $user = $em->getRepository(Participant::class)->find($id);
        $user->setAdministrateur(1);
        $em->flush();
        $this->addFlash("success", "l'utilisateur a maintenant les droits d'administrateur.");
        return $this->redirectToRoute('admin_ListeUtilisateurs');

    }

    /**
     * @Route("/annulerAdmin{id}",name="annulerAdmin")
     */
    public function annulerAdmin($id,Request $request, EntityManagerInterface $em){
        $user = $em->getRepository(Participant::class)->find($id);

        $user->setAdministrateur(0);
        $em->flush();
        $this->addFlash("success", "l'utilisateur n'a plus les droits d'administrateur.");
        return $this->redirectToRoute('admin_ListeUtilisateurs');

    }
}


