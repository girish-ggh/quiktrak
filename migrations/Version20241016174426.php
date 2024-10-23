<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241016174426 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inspector CHANGE location location ENUM(\'UK\', \'Mexico\', \'India\')');
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F8F4BD7827');
        $this->addSql('DROP INDEX IDX_FBD8E0F8F4BD7827 ON job');
        $this->addSql('ALTER TABLE job ADD inspector_id INT NOT NULL, ADD title VARCHAR(255) NOT NULL, ADD scheduled_date DATETIME NOT NULL, ADD status VARCHAR(50) NOT NULL, ADD assessment LONGTEXT DEFAULT NULL, DROP assigned_to_id, DROP assignment_date, DROP completed, DROP rating, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE completed_at completion_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8D0E3F35F FOREIGN KEY (inspector_id) REFERENCES inspector (id)');
        $this->addSql('CREATE INDEX IDX_FBD8E0F8D0E3F35F ON job (inspector_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F8D0E3F35F');
        $this->addSql('DROP INDEX IDX_FBD8E0F8D0E3F35F ON job');
        $this->addSql('ALTER TABLE job ADD assigned_to_id INT DEFAULT NULL, ADD assignment_date DATE DEFAULT NULL, ADD completed TINYINT(1) NOT NULL, ADD rating INT DEFAULT NULL, DROP inspector_id, DROP title, DROP scheduled_date, DROP status, DROP assessment, CHANGE description description LONGTEXT NOT NULL, CHANGE completion_date completed_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES inspector (id)');
        $this->addSql('CREATE INDEX IDX_FBD8E0F8F4BD7827 ON job (assigned_to_id)');
        $this->addSql('ALTER TABLE inspector CHANGE location location VARCHAR(255) DEFAULT NULL');
    }
}
