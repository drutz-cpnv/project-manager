<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220521132025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE note ADD name VARCHAR(255) NOT NULL, DROP type');
        $this->addSql('ALTER TABLE personal_evaluation DROP prject');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE note ADD type INT NOT NULL, DROP name');
        $this->addSql('ALTER TABLE personal_evaluation ADD prject VARCHAR(255) NOT NULL');
    }
}
