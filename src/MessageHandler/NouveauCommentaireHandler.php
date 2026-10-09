<?php

namespace App\MessageHandler;

use App\Message\NouveauCommentaire;
use App\Repository\CommentaireRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NouveauCommentaireHandler
{
    public function __construct(
        private CommentaireRepository $commentaireRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(NouveauCommentaire $message): void
    {
        

        $commentaire = $this->commentaireRepository->find(
            $message->getCommentaireId()
        );

        if (!$commentaire) {
            return;
        }

        sleep(5);

        $this->logger->info('Nouveau commentaire traité.', [
            'commentaire_id' => $commentaire->getId(),
            'restaurant' => $commentaire->getRestaurant()?->getNom(),
            'user' => $commentaire->getUser()?->getUserIdentifier(),
        ]);
    }
}