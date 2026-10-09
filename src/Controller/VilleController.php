<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VilleController extends AbstractController
{
    #[Route('/ville', name: 'app_ville')]
    public function index(): Response
    {
        return $this->render('ville/index.html.twig', [
            'controller_name' => 'VilleController',
        ]);
    }

    #[Route('/ville/new', name: 'app_ville_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ville = new Ville();

        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ville);
            $entityManager->flush();

            $this->addFlash('success', 'Ville créée avec succès.');

            return $this->redirectToRoute('app_ville');
        }

        return $this->render('ville/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/ville/{id}/edit', name: 'app_ville_edit')]
    public function edit(
            int $id,
            Request $request,
            EntityManagerInterface $entityManager
    ): Response {
        $ville = $entityManager->getRepository(Ville::class)->find($id);

        if (!$ville) {
            throw $this->createNotFoundException('Ville non trouvée.');
        }

        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Ville modifiée avec succès.');

            return $this->redirectToRoute('app_ville');
        }

        return $this->render('ville/edit.html.twig', [
            'form' => $form,
            'ville' => $ville,
        ]);
    }
}
