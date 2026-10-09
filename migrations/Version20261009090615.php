<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261009090615 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
{
    $this->addSql('ALTER TABLE commentaire ADD user_id INT DEFAULT NULL');
    $this->addSql('UPDATE commentaire SET user_id = 2 WHERE user_id IS NULL');
    $this->addSql('ALTER TABLE commentaire ALTER COLUMN user_id SET NOT NULL');
    $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE');
    $this->addSql('CREATE INDEX IDX_67F068BCA76ED395 ON commentaire (user_id)');
}

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP CONSTRAINT FK_67F068BCA76ED395');
        $this->addSql('DROP INDEX IDX_67F068BCA76ED395');
        $this->addSql('ALTER TABLE commentaire DROP user_id');
    }
}
