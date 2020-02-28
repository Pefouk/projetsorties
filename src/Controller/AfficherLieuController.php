<?php

namespace App\Controller;

use App\Entity\Lieu;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AfficherLieuController extends AbstractController
{
    /**
     * @Route("/afficher/lieu/{id}", name="afficher_lieu", requirements={"id": "\d+"})
     */
    public function afficherLieu($id)
    {
        $lieuRepo = $this->getDoctrine()->getRepository(Lieu::class);
        $lieu = $lieuRepo->find($id);


        return $this->json(array('ville'=>$lieu->getVille()->getNom(), 'rue'=>$lieu->getRue(), 'cp'=>$lieu->getVille()->getCodePostale(), 'latitude'=>$lieu->getLatitude(), 'longitude'=>$lieu->getLatitude()));
    }
}
