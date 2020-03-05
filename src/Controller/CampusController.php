<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CampusController
 * @package App\Controller
 * @Route("/campus", name="campus_")
 */
class CampusController extends AbstractController
{
    /**
     * @Route("/liste", name="liste")
     */
    public function liste()
    {
        $sites = $this->getDoctrine()->getRepository('App:Campus')->findAll();

        return $this->render('campus/liste.html.twig', ['sites' => $sites]);
    }
}
