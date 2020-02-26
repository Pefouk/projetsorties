<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package App\Controller
 * @Route("/", name="home")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="_home")
     */
    public function index()
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        } else {
            return $this->redirectToRoute('sorties_afficher');
        }
    }

}
