<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\FiltrerSortiesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\AlreadySubmittedException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
            $res = $this->sortiesTri($form, $entityManager);
        else {
            $res = $entityManager->getRepository(Sortie::class)->findBy(['campus' => $this->getUser()->getCampus()]);
        }
        return $this->render('sortie_afficher/index.html.twig', ['sorties' => $res, 'form' => $form->createView()]);
    }

    private function sortiesTri(FormInterface $form, $entityManager)
    {
        if ($form->getData()['campus'] === null) {
            $this->addFlash('danger', 'Campus invalide !');
            return null;
        }
        $sortiesRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $res = $sortiesRepo->findBy(['campus' => $form->getData()['campus']->getID()]);
        return $res;
    }
}