<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220509105346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project DROP kick_off, DROP specifications, DROP planning, DROP prototype, DROP good_for_print, DROP final');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project ADD kick_off INT DEFAULT NULL, ADD specifications INT DEFAULT NULL, ADD planning INT DEFAULT NULL, ADD prototype INT DEFAULT NULL, ADD good_for_print INT DEFAULT NULL, ADD final INT DEFAULT NULL');
    }
}
