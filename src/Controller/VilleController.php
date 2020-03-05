<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\FiltrerVilleType;
use App\Form\ModifierVilleType;
use App\Form\SupprimerCampusType;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ville", name="ville_")
 */
class VilleController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/liste", name="liste")
     */
    public function liste(Request $request)
    {
        $form = $this->createForm(FiltrerVilleType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form->get('nom')->getViewData();
        } else {
            $name = null;
        }
        $villes = $this->getDoctrine()->getRepository('App:Ville')->findByName($name);
        return $this->render('ville/liste.html.twig', ['villes' => $villes, 'form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/modifier/{id}", name="modifier")
     */
    public function modifier(Request $request, int $id, EntityManagerInterface $entityManager)
    {
        $ville = $this->getDoctrine()->getRepository(Ville::class)->find($id);
        $form = $this->createForm(ModifierVilleType::class, $ville);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ville);
            $entityManager->flush();
            $this->addFlash('success', 'Le site a bien été modifié !');
            return $this->redirectToRoute('ville_liste');
        } elseif ($form->isSubmitted()) {
            $this->addFlash('danger', 'La modification du site est incorrect !');
        }
        return $this->render('ville/modifier.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/supprimer/{id}", name="supprimer")
     */
    public function supprimer(Request $request, int $id, EntityManagerInterface $entityManager)
    {
        $ville = $this->getDoctrine()->getRepository(Ville::class)->find($id);
        $form = $this->createForm(SupprimerCampusType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->remove($ville);
            $entityManager->flush();
            $this->addFlash('success', 'Le site a bien été supprimé !');
            return $this->redirectToRoute('ville_liste');
        } elseif ($form->isSubmitted()) {
            $this->addFlash('danger', 'La suppression du site est incorrect !');
        }
        return $this->render('ville/supprimer.html.twig', ['form' => $form->createView(), 'nom' => $ville->getNom()]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @Route("/nouveau", name="creer")
     * @return RedirectResponse|Response
     */
    public function nouveau(Request $request, EntityManagerInterface $entityManager)
    {
        $ville = new Ville();
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ville);
            $entityManager->flush();
            $this->addFlash('success', 'Nouvelle ville créé !');
            return $this->redirectToRoute('ville_liste');
        }
        return $this->render('ville/creer.html.twig', ['form' => $form->createView()]);
    }
}