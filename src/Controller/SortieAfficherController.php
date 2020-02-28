<?php

namespace App\Controller;

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
}