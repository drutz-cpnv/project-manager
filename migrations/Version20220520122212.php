<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220520122212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE note (id INT AUTO_INCREMENT NOT NULL, student_evaluation_id INT NOT NULL, type INT NOT NULL, value DOUBLE PRECISION NOT NULL, comment LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_CFBDFA1410F0AED8 (student_evaluation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personal_evaluation (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, coach_id INT NOT NULL, prject VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_53F302D5CB944F1A (student_id), INDEX IDX_53F302D53C105691 (coach_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA1410F0AED8 FOREIGN KEY (student_evaluation_id) REFERENCES personal_evaluation (id)');
        $this->addSql('ALTER TABLE personal_evaluation ADD CONSTRAINT FK_53F302D5CB944F1A FOREIGN KEY (student_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE personal_evaluation ADD CONSTRAINT FK_53F302D53C105691 FOREIGN KEY (coach_id) REFERENCES person (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA1410F0AED8');
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP TABLE personal_evaluation');
    }
}
