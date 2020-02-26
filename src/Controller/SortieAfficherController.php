<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\FiltrerSortiesType;
use Doctrine\ORM\EntityManagerInterface;
use http\Client\Curl\User;
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
        if ($form->isSubmitted() && $form->isValid() && $this->verifierForm($form))
            $res = $this->getDoctrine()->getRepository(Sortie::class)->findByCampusAndNom($form, $this->getUser());
        else {
            $res = $entityManager->getRepository(Sortie::class)->findByCampus($this->getUser()->getCampus());
        }
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
}