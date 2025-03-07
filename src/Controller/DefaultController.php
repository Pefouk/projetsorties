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
            $this->addFlash('danger', 'Merci de vous connecter !');
            return $this->redirectToRoute('app_login');
        } else {
            $this->addFlash('success', 'Bienvenue ' . $this->getUser()->getUsername() . ' !');
            return $this->redirectToRoute('sorties_afficher');
        }
    }

}
