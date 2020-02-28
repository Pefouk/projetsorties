<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\FiltrerSortiesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SortieAfficherController
 * @package App\Controller
 * @Route("/sortie", name="sorties_")
 */
class SortieAfficherController extends AbstractController
{
    /**
     * @Route("/detail/{id}", name="detail")
     * @param $id
     * @return Response
     */
    public function afficherSortie(int $id)
    {
        if (!$this->getUser() instanceof Participant) {
            $this->addFlash('danger', 'Merci de vous connecter avant de poursuivre !');
            return $this->redirectToRoute('app_login');
        }
        $res = $this->getDoctrine()->getRepository(Sortie::class)->findbyId($id);
        return $this->render('sortie_afficher/detail.html.twig', ['sortie' => $res]);
    }

    /**
     * @param $request
     * @param int $id
     * @return Response
     * @Route("/publier/{id}", name="publier")
     */
    public function publierSortie(Request $request, int $id, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $sortie = $this->getDoctrine()->getRepository(Sortie::class)->findbyId($id);
        $referer = $request->headers->get('referer');

        if (!$user instanceof Participant) {
            $this->addFlash('danger', 'Merci de vous connecter avant de poursuivre !');
            return $this->redirectToPreviousOrListe($referer);
        }
        if ($sortie->getOrganise() != $user && !in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $this->addFlash('danger', 'Cette sortie n\'est pas la votre et vous n\'êtes pas administrateur !');
            return $this->redirectToPreviousOrListe($referer);
        }
        if ($sortie->getEtat()->getLibelle() !== 'Créée') {
            $this->addFlash('danger', 'Impossible de publier la sortie car elle n\'est pas en êtat Créée !');
            return $this->redirectToPreviousOrListe($referer);
        }
        $sortie->setEtat($this->getDoctrine()->getRepository(Etat::class)->find(2));
        $entityManager->persist($sortie);
        $entityManager->flush();
        $this->addFlash('success', 'La sortie est désormais ouverte !');
        return $this->redirectToPreviousOrListe($referer);
    }

    /**
     * @Route("/liste", name="afficher")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        if (!$this->getUser() instanceof Participant) {
            $this->addFlash('danger', 'Merci de vous connecter avant de poursuivre !');
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(FiltrerSortiesType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $this->verifierForm($form)) {
            $res = $this->getDoctrine()->getRepository(Sortie::class)->findByCampusAndNom($form, $this->getUser());
            if ($form->getData()['nonInscrit'])
                foreach ($res as $cle => $sortie) {
                    if ($sortie->isInscrit($this->getUser()))
                        unset($res[$cle]);
                }
            if ($form->getData()['inscrit'])
                foreach ($res as $cle => $sortie) {
                    if (!$sortie->isInscrit($this->getUser()))
                        unset($res[$cle]);
                }
        } else {
            $res = $entityManager->getRepository(Sortie::class)->findByCampus($this->getUser()->getCampus());
        }
        return $this->render('sortie_afficher/liste.html.twig', ['sorties' => $res, 'form' => $form->createView()]);
    }

    private function verifierForm(FormInterface $form)
    {
        $campus = $form->getData()['campus'];
        $datemin = $form->getData()['datemin'];
        $datemax = $form->getData()['datemax'];
        if ($campus === null) {
            $this->addFlash('danger', 'Campus invalide !');
            return false;
        }
        if (($datemin instanceof \DateTime && $datemax instanceof \DateTime && $datemin->getTimestamp() > $datemax->getTimestamp()) ||
            (!$datemin instanceof \DateTime && $datemax instanceof \DateTime) ||
            ($datemin instanceof \DateTime && !$datemax instanceof \DateTime)) {
            $this->addFlash('danger', 'Dates invalides !');
            return false;
        }
        return true;
    }

    private function redirectToPreviousOrListe($referer)
    {
        if ($referer == null)
            return $this->redirectToRoute('sorties_afficher');
        else
            return $this->redirect($referer);
    }
}