<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200304092341 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F2A1AC22D9');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F2A1AC22D9 FOREIGN KEY (organise_id) REFERENCES participant (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F2A1AC22D9');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F2A1AC22D9 FOREIGN KEY (organise_id) REFERENCES participant (id)');
    }
}
