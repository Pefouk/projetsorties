<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\FiltrerCampusType;
use App\Form\ModifierCampusType;
use App\Form\NouveauCampusType;
use App\Form\SupprimerCampusType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CampusController
 * @package App\Controller
 * @Route("/campus", name="campus_")
 */
class CampusController extends AbstractController
{

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @Route("/nouveau", name="creer")
     * @return RedirectResponse|Response
     */
    public function creerCampus(Request $request, EntityManagerInterface $entityManager)
    {
        $campus = new Campus();
        $form = $this->createForm(NouveauCampusType::class, $campus);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($campus);
            $entityManager->flush();
            $this->addFlash('success', 'Nouveau site créé !');
            return $this->redirectToRoute('campus_liste');
        }
        return $this->render('campus/creer.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @param EntityManagerInterface $entityManager
     * @Route("/supprimer/{id}", name="supprimer")
     * @return Response
     */
    public function supprimer(Request $request, int $id, EntityManagerInterface $entityManager)
    {
        $campus = $this->getDoctrine()->getRepository(Campus::class)->find($id);
        $form = $this->createForm(SupprimerCampusType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->remove($campus);
            $entityManager->flush();
            $this->addFlash('success', 'Le site a bien été supprimé !');
            return $this->redirectToRoute('campus_liste');
        } elseif ($form->isSubmitted()) {
            $this->addFlash('danger', 'La suppression du site est incorrect !');
        }
        return $this->render('campus/supprimer.html.twig', ['form' => $form->createView(), 'nom' => $campus->getNom()]);
    }

    /**
     * @Route("/modifier/{id}", name="modifier")
     * @param Request $request
     * @param int $id
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function modifier(Request $request, int $id, EntityManagerInterface $entityManager)
    {
        $campus = $this->getDoctrine()->getRepository(Campus::class)->find($id);
        $form = $this->createForm(ModifierCampusType::class, $campus);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $campus->setNom($form->get('nom')->getData());
            $entityManager->persist($campus);
            $entityManager->flush();
            $this->addFlash('success', 'Le site a bien été modifié !');
            return $this->redirectToRoute('campus_liste');
        } elseif ($form->isSubmitted()) {
            $this->addFlash('danger', 'La modification du site est incorrect !');
        }
        return $this->render('campus/modifier.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/liste", name="liste")
     * @param Request $request
     * @return Response
     */
    public function liste(Request $request)
    {
        $form = $this->createForm(FiltrerCampusType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form->get('nom')->getViewData();
        } else {
            $name = null;
        }
        $sites = $this->getDoctrine()->getRepository('App:Campus')->findByName($name);
        return $this->render('campus/liste.html.twig', ['sites' => $sites, 'form' => $form->createView()]);
    }
}
