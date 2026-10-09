<?php

namespace App\Message;

class NouveauCommentaire
{
    public function __construct(
        private int $commentaireId
    ) {
    }

    public function getCommentaireId(): int
    {
        return $this->commentaireId;
    }
}