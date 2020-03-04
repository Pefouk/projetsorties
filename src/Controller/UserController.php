<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\ModifierProfilType;
use App\Form\MotDePasseOublieType;
use App\Form\MotifAnnulationType;
use App\Form\ProfilType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/oublie/", name="MotDePasseOublié")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Swift_Mailer $mailer
     * @return Response
     * @throws Exception
     */
    public function motDePasseOublie(Request $request, EntityManagerInterface $entityManager, Swift_Mailer $mailer)
    {
        $form = $this->createForm(MotDePasseOublieType::class)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $account = $entityManager->getRepository(Participant::class)->findBy(['mail' => $form->getData()['mail']]);
            if (count($account) == 1) {
                $account = $account[0];
                $account->setUrlMdp(hash('md5', random_bytes(10)));
                $account->setDateMdpOublie(new \DateTime());
                $entityManager->persist($account);
                $entityManager->flush();
                $this->mailMotDePasse($account->getUrlMdp(), $mailer, $account->getPseudo(), $account->getMail());
            }
            $this->addFlash("success", "Un mail a été envoyé avec le lien pour réinitialiser votre mot de passe !");
            return $this->redirectToRoute('app_login');
        }
        return $this->render('user/oublie.html.twig', ['form' => $form->createView()]);
    }

    private function mailMotDePasse(?string $getUrlMdp, Swift_Mailer $mailer, $pseudo, $mail)
    {
        $message = new Swift_Message('Mot de passe oublié');
        $message->setFrom('projetsortir35@gmail.com')
            ->setTo($mail)
            ->setBody(
                $this->renderView('mail/mdpOublie.html.twig', ['url' => $getUrlMdp, 'pseudo' => $pseudo]), 'text/html'
            );
        $mailer->send($message);
    }

    /**
     * @Route("/oublie/{id}", name="lienMail")
     * @param $id
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function lienMail($id, Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager)
    {
        $ajd = new \DateTime();
        $ajd->add(date_interval_create_from_date_string('1 hour'));
        $user = $this->getDoctrine()->getRepository(Participant::class)->findBy(['urlMdp' => $id]);
        if (count($user) === 1 && $user[0]->getDateMdpOublie() < $ajd)
            return $this->changerMotDePasse($user[0], $request, $encoder, $entityManager);
        else {
            throw $this->createAccessDeniedException();
        }
    }

    private function changerMotDePasse(Participant $participant, Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm('App\Form\ChangerMotDePasseType');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($participant, $form->get('motPasse')->getData());
            $participant->setMotPasse($hash);
            $participant->setUrlMdp(null);
            $entityManager->persist($participant);
            $entityManager->flush();
            $this->addFlash('success', 'Mot de passe modifé avec succes.');
            return $this->redirectToRoute('sorties_afficher');
        }
        return $this->render('user/changerMDP.html.twig', ['form' => $form->createView(), 'participant' => $participant]);
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $entityManager
     * @Route("/user/changermdp", name="changerMDP")
     * @return RedirectResponse|Response
     */
    public function changerMotDePasseConnecte(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        if ($user instanceof Participant)
            return $this->changerMotDePasse($user, $request, $encoder, $entityManager);
        else
            throw $this->createAccessDeniedException();
    }

    /**
     * @Route("/user/profil/{id}", name="profil", requirements={"id": "\d+"})
     * @param $id
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
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
     * @param $id
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return RedirectResponse|Response
     * @throws Exception
     */
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
        if ($updateForm->isSubmitted() && $updateForm->isValid()) {
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
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function sinscrire($id, Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $sortieRepo = $em->getRepository(Sortie::class);
        $sortie = $sortieRepo->findbyId($id);
        if ($user->isInscrit($sortie))
            $this->addFlash('danger', 'Vous êtes déja inscrit a cette sortie !');
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
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     * @throws Exception
     */
    public function desinscrire($id, Request $request, EntityManagerInterface $em)
    {
        $ajd = new DateTime();
        $user = $this->getUser();
        $sortieRepo = $em->getRepository(Sortie::class);
        $sortie = $sortieRepo->findbyId($id);
        if (!$user->isInscrit($sortie))
            $this->addFlash('danger', 'Vous n\'êtes pas inscrit a cette sortie !');
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
     * @Route("/annulerMaSortie/{id}",name="annulerMaSortie")
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     */
    public function annulerMaSortie($id, Request $request, EntityManagerInterface $em)
    {
        $motifForm = $this->createForm(MotifAnnulationType::class);
        $motifForm->handleRequest($request);
        $sortieRepo = $em->getRepository(Sortie::class);
        $sortie = $sortieRepo->findbyId($id);
        $user = $this->getUser();
        $etatRepo = $em->getRepository(Etat::class);
        $etat = $etatRepo->find(6);
        $motif = $motifForm->get('MotifAnnulation')->getData();
        $sortie->setMotifAnnulation($motif);
        $sortie->setEtat($etat);

        $listInscrit = $sortie->getInscrit();

        foreach ($listInscrit as $sortieinscrit){
            if ($sortieinscrit->isInscrit($sortie)){
                $sortie->removeInscrit($sortieinscrit);
            }
         }



        if ($motifForm->isSubmitted() && $motifForm->isValid() && $user == $sortie->getOrganise()) {
            $em->flush($sortie);
            $this->addFlash('success', 'Votre sortie "' . $sortie->getNom() . '" a bien été annulée !');
            return $this->redirectToRoute('sorties_afficher');
        }
        return $this->render('sortie_afficher/annulerSortie.html.twig', [
            "motifForm" => $motifForm->createView(),
            "sortie" => $sortie
        ]);
    }
}


