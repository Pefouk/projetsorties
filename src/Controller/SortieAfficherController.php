<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\FiltrerSortiesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

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
        //$this->fakeConnect(1);
        $sortiesRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $form = $this->createForm(FiltrerSortiesType::class);
        $res = $sortiesRepo->findBy(['campus' => $this->getUser()->getCampus()]);
        return $this->render('sortie_afficher/index.html.twig', ['sorties' => $res, 'form' => $form->createView()]);
    }

    private function fakeConnect(int $id) {
        $user = $this->getDoctrine()->getRepository(Participant::class)->find(['id' => $id]);
        $usertoken = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->get('security.token_storage')->setToken($usertoken);
    }
}