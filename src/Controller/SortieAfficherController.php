<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\FiltrerSortiesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Class SortieAfficherController
 * @package App\Controller
 * @Route("/sorties", name="sorties_")
 */
class SortieAfficherController extends AbstractController
{
    /**
     * @Route("", name="afficher")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(FiltrerSortiesType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
            $res = $this->sortiesTri($form);
        else {
            $res = $entityManager->getRepository(Sortie::class)->findBy(['campus' => $this->getUser()->getCampus()]);
        }
        dump($res);
        return $this->render('sortie_afficher/index.html.twig', ['sorties' => $res, 'form' => $form->createView()]);
    }

    private function verifierForm(FormInterface $form)
    {
        $campus = $form->getData()['campus'];
        $datemin = $form->getData()['datemin'];
        $datemax = $form->getData()['datemax'];
        if ($campus === null) {
            $this->addFlash('danger','Campus invalide !');
            return false;
        }
        if (($datemin instanceof \DateTime && $datemax instanceof \DateTime && $datemin->getTimestamp() > $datemax->getTimestamp()) ||
            (!$datemin instanceof \DateTime && $datemax instanceof \DateTime) ||
            ($datemin instanceof  \DateTime && !$datemax instanceof  \DateTime)) {
            $this->addFlash('danger', 'Dates invalides !');
            return false;
        }
        return true;
    }

    /*
     * La fonction sortiesTri permet de récuperer les données du formulaire et retourne
     * un tableau de Sorties avec toutes les sorties correspondant au filtre appliqué
     * via le formulaire.
     */
    private function sortiesTri(FormInterface $form)
    {
        if ($this->verifierForm($form) === false)
            return null;
        $datemin = $form->getData()['datemin'];
        $datemax = $form->getData()['datemax'];
        $campus = $form->getData()['campus'];
        $sortiesRepo = $this->getDoctrine()->getRepository(Sortie::class);
        if ($form->getData()['recherche'])
            $nom = '%' . $form->getData()['recherche'] . '%';
        else
            $nom = '%';
        if ($datemin instanceof \DateTime && $datemax instanceof \DateTime)
            $res = $sortiesRepo->findByCampusNomAndDate($campus, $nom, $datemin, $datemax);
        else
           $res = $sortiesRepo->findByCampusAndNom($campus, $nom);
        return $res;
    }

    /*
     *"campus" => campus entity
     *"recherche" => "f"
     *"datemin" => null
     *"datemax" => null
     *"organise" => false
     *"inscrit" => false
     *"nonInscrit" => false
     *"passee" => false
     */
}