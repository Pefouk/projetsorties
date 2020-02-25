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
        return $this->render('home/home.html.twig');
    }

    /**
     * @Route("/mon_profil", name="mon_profil")
     */
    /*Cette fonction renvoie la page qui permet dafficher le profil de lutilisateur connecté*/
    public function afficherMonProfil()
    {

        return $this->render('user/myprofil.html.twig');
    }

}
