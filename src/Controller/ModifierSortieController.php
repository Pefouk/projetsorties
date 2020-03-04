<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Exception\SortieException;
use App\Form\LieuType;
use App\Form\ModifierProfilType;
use App\Form\ModifierSortieType;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ModifierSortieController extends AbstractController
{
    /**
     * @Route("/modifier/sortie/{id}", name="modifier_sortie", requirements={"id":"\d+"})
     */
    public function modifierSortie($id, Request $request, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");
        /*Récupérer la sortie à modifier*/
        $em = $this->getDoctrine()->getManager();

        $etatRep = $this->getDoctrine()->getRepository(Etat::class);

        $sortie = $em->getRepository(Sortie::class)->find($id);
        $updatelieu = $sortie->getLieu();
        $updateville = $sortie->getLieu()->getVille();
        $organisateur = $sortie->getOrganise();

        /*Creation des formulaires*/
        $updateForm = $this->createForm(ModifierSortieType::class, $sortie);
        $lieuForm = $this->createForm(LieuType::class, $updatelieu);
        $villeForm = $this->createForm(VilleType::class, $updateville);

        /*Récupérer la request*/
        $updateForm->handleRequest($request);
        $lieuForm->handleRequest($request);
        $villeForm->handleRequest($request);

        if($organisateur instanceof Participant)
        {
            if($updateForm->isSubmitted() && $updateForm->isValid()) {
                if ($updateForm->getClickedButton() === $updateForm->get('enregistrer')) {
                    $etat = $etatRep->find(1);
                    $sortie->setEtat($etat);

                    $em->persist($sortie);
                    $em->flush();
                    $this->addFlash("success", "Votre sortie a bien été modifiée !");
                    return $this->redirectToRoute('sorties_afficher');
                }
                if ($updateForm->getClickedButton() === $updateForm->get('publier')) {
                    $etat = $etatRep->find(2);
                    $sortie->setEtat($etat);

                    $em->persist($sortie);
                    $em->flush();
                    $this->addFlash("success", "Votre sortie a bien été publiée !");
                    return $this->redirectToRoute('sorties_afficher');
                }
            }try {
            $em->persist($sortie);
            $em->flush();
            $this->addFlash("success", "Votre sortie a bien été créée.");
        } catch (SortieException $e) {
            $this->addFlash("danger", $e->getMessage());
        }
        }

        /*if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            $em->persist($updatelieu);
            $em->flush();

            $this->addFlash("success", "Votre lieu a bien été ajouté à la liste !");
            return $this->redirectToRoute('creer_sortie');
        }

        if($villeForm->isSubmitted())
        {
            $em->persist($updateville);
            $em->flush();

            $this->addFlash("success","Ville ajoutée !");
        }*/

        return $this->render('sortie/modifiersortie.html.twig', [
        "updateForm"=>$updateForm->createView(),
            "villeForm"=>$villeForm->createView(),
            "lieuForm"=>$lieuForm->createView(),
            "sortie"=>$sortie
        ]);
    }
}
