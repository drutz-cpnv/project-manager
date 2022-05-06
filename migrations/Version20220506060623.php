<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220506060623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project ADD mandate_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE6C1129CD FOREIGN KEY (mandate_id) REFERENCES mandate (id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE6C1129CD ON project (mandate_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE6C1129CD');
        $this->addSql('DROP INDEX IDX_2FB3D0EE6C1129CD ON project');
        $this->addSql('ALTER TABLE project DROP mandate_id');
    }
}
