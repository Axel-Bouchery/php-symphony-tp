<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\User;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommentaireController extends AbstractController
{
    #[Route('/commentaire', name: 'app_commentaire')]
    public function index(): Response
    {
        return $this->render('commentaire/index.html.twig', [
            'controller_name' => 'CommentaireController',
        ]);
    }

    #[Route('/commentaire/new', name: 'app_commentaire_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $commentaire = new Commentaire();
        $commentaire->setDate(new \DateTime());

        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            if (!$user instanceof User) {
                throw $this->createAccessDeniedException();
            }

            $commentaire->setUser($user);

            $entityManager->persist($commentaire);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire créé avec succès.');

            return $this->redirectToRoute('app_commentaire');
        }

        return $this->render('commentaire/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/commentaire/{id}/edit', name: 'app_commentaire_edit')]
    public function edit(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $commentaire = $entityManager
            ->getRepository(Commentaire::class)
            ->find($id);

        if (!$commentaire) {
            throw $this->createNotFoundException('Commentaire non trouvé.');
        }

        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire modifié avec succès.');

            return $this->redirectToRoute('app_commentaire');
        }

        return $this->render('commentaire/edit.html.twig', [
            'form' => $form,
            'commentaire' => $commentaire,
        ]);
    }
}
