<?php

namespace App\Controller;

use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SortieAfficherController
 * @package App\Controller
 * @Route("/sorties", name="sorties_")
 */
class SortieAfficherController extends AbstractController
{
    /**
     * @Route("/", name="afficher")
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        $sortiesRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $res = $sortiesRepo->findAll();
        return $this->render('sortie_afficher/index.html.twig', ['sorties' => $res]);
    }
}
