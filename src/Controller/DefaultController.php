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
}
