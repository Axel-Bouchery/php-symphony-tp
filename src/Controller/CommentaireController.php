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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Message\NouveauCommentaire;
use Symfony\Component\Messenger\MessageBusInterface;

final class CommentaireController extends AbstractController
{
    #[Route('/commentaire', name: 'app_commentaire')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $commentaires = $entityManager
            ->getRepository(Commentaire::class)
            ->findAll();

        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaires,
        ]);
    }

    #[Route('/commentaire/new', name: 'app_commentaire_new')]
    #[IsGranted('ROLE_USER')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        MessageBusInterface $messageBus
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

            $messageBus->dispatch(
            new NouveauCommentaire($commentaire->getId())
        );

        $this->addFlash('success', 'Commentaire créé avec succès.');

            return $this->redirectToRoute('app_commentaire');
        }

        return $this->render('commentaire/new.html.twig', [
            'form' => $form,
        ]);
    }

        #[Route('/commentaire/{id}/delete', name: 'app_commentaire_delete')]
    public function delete(
        int $id,
        EntityManagerInterface $entityManager
    ): Response {
        $commentaire = $entityManager
            ->getRepository(Commentaire::class)
            ->find($id);

        if (!$commentaire) {
            throw $this->createNotFoundException('Commentaire non trouvé.');
        }

        $user = $this->getUser();

        if ($commentaire->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $entityManager->remove($commentaire);
        $entityManager->flush();

        $this->addFlash('success', 'Commentaire supprimé avec succès.');

        return $this->redirectToRoute('app_commentaire');
    }
}
