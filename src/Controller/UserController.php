<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\ModifierProfilType;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/oublie/", name="MotDePasseOublié")
     */
    public function motDePasseOublie()
    {
        return $this->render('user/oublie.html.twig');
    }

    /**
     * @Route("/user/profil/{id}", name="profil", requirements={"id": "\d+"})
     */
    /*Cette fonction renvoie la page qui permet d'afficher le profil de l'utilisateur connecté afin qu'il le modifie*/
    public function afficherMonProfil($id, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        dump($this->getUser()->getRoles());
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
        if ($this->getUser()->getId() != $id && !in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $this->addFlash('danger', 'Vous ne pouvez pas modifier le profil de quelqu\'un d\'autre !');
            return $this->redirectToRoute('sorties_afficher');
        }
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
            return $this->redirectToRoute('profil', ['id' => $profil->getId()]);
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
        if ($user->isInscrit($sortie))
            $this->addFlash('danger','Vous êtes déja inscrit a cette sortie !');
        else if ($sortie->getEtat()->getLibelle() === 'Ouverte') {
            $user->addParticipe($sortie);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Vous vous êtes inscrit à la sortie : ' . $sortie->getNom() . '!');
        } else {
            $this->addFlash('danger', 'Vous ne pouvez pas vous inscrire a cette sortie car les inscriptions ne sont pas ouvertes !');
        }
        $referer = $request->headers->get('referer');
        return $this->redirectToPreviousOrListe($referer);
    }

    private function redirectToPreviousOrListe($referer)
    {
        if ($referer == null)
            return $this->redirectToRoute('sorties_afficher');
        else
            return $this->redirect($referer);
    }

    /**
     * @Route("/desinscrire/{id}",name="desinscrire")
     */
    public function desinscrire($id, Request $request, EntityManagerInterface $em)
    {
        $ajd = new \DateTime();
        $user = $this->getUser();
        $sortieRepo = $em->getRepository(Sortie::class);
        $sortie = $sortieRepo->findbyId($id);
        if (!$user->isInscrit($sortie))
            $this->addFlash('danger','Vous n\'êtes pas inscrit a cette sortie !');
        else if ($sortie->getEtat()->getLibelle() === 'Ouverte' || ($sortie->getEtat()->getLibelle() === 'Clôturée' && $ajd < $sortie->getDateLimiteInscription())) {
            $user->removeParticipe($sortie);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'tu t\'es désisté à : ' . $sortie->getNom() . '!');
        } else {
            $this->addFlash('danger', 'Vous ne pouvez pas vous desinscrire a cette sortie car la date de cloture est passée !');
        }
        $referer = $request->headers->get('referer');
        return $this->redirectToPreviousOrListe($referer);
    }

    /**
     * @Route("/admin/ListeUtilisateurs",name="ListeUtilisateurs")
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
     * @Route("/admin/desactiver/{id}",name="desactiver")
     */

    public function desactiver(Request $request, EntityManagerInterface $em, $id)
    {

        $entity = $em->getRepository(Participant::class)->find($id);

        if ($entity != null) {
            $entity->setActif(0);
            $em->flush();
            return $this->redirectToRoute('ListeUtilisateurs');


        }
    }

    /**
     * @Route("/admin/activer/{id}",name="activer")
     */

    public function activer(Request $request, EntityManagerInterface $em, $id)
    {

        $entity = $em->getRepository(Participant::class)->find($id);

        if ($entity != null) {
            $entity->setActif(1);
            $em->flush();
            return $this->redirectToRoute('ListeUtilisateurs');


        }
    }

    /**
     * @Route("/admin/supprimer/{id}",name="supprimer")
     */
    public function supprimer(Request $request, EntityManagerInterface $em, $id)
    {

        $entity = $em->getRepository(Participant::class)->find($id);

        if ($entity != null) {
            $em->remove($entity);
            $em->flush();

            return $this->redirectToRoute('ListeUtilisateurs');
        }
    }

    /**
     * @Route("/annulerMaSortie/{id}",name="annulerMaSortie")
     */
    public function annulerMaSortie($id, Request $request, EntityManagerInterface $em){

        $user = $this->getUser();
        $sortieRepo = $em->getRepository(Sortie::class);
        $sortie = $sortieRepo->findbyId($id);
        $etatRepo = $em->getRepository(Etat::class);
        $etat = $etatRepo->find(6);

        if ($user==$sortie->getOrganise()){
            $sortie->setEtat($etat);
            $em->flush($sortie);
            $this->addFlash('success', 'Votre sortie "' . $sortie->getNom() . '" a bien été annulée !');
        }

            $referer = $request->headers->get('referer');
            return $this->redirectToPreviousOrListe($referer);


    }
/**
 * @Route("/annulerSortie/{id}",name="annulerSortie")
 */
public function annulerSortie($id, Request $request, EntityManagerInterface $em){

    $sortieRepo = $em->getRepository(Sortie::class);
    $sortie = $sortieRepo->find($id);
    $user = $this->getUser();

    return $this->render("sortie_afficher/annulerSortie.html.twig",[
        "sortie"=>$sortie,
        "user"=>$user
    ]);
}


}
