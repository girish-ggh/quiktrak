<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241016171621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE inspector (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL, location ENUM(\'UK\', \'Mexico\', \'India\'), UNIQUE INDEX UNIQ_72DD518BE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job (id INT AUTO_INCREMENT NOT NULL, assigned_to_id INT DEFAULT NULL, description LONGTEXT NOT NULL, assignment_date DATE DEFAULT NULL, completed TINYINT(1) NOT NULL, completed_at DATETIME DEFAULT NULL, rating INT DEFAULT NULL, INDEX IDX_FBD8E0F8F4BD7827 (assigned_to_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES inspector (id)');
        $this->addSql('ALTER TABLE jobs DROP FOREIGN KEY jobs_ibfk_1');
        $this->addSql('DROP TABLE jobs');
        $this->addSql('DROP TABLE inspectors');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE jobs (id INT AUTO_INCREMENT NOT NULL, assigned_to INT DEFAULT NULL, description TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, assignment_date DATE DEFAULT NULL, completed TINYINT(1) DEFAULT 0, completed_at DATETIME DEFAULT NULL, rating INT DEFAULT NULL, INDEX assigned_to (assigned_to), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE inspectors (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, email VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, location VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, UNIQUE INDEX email (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE jobs ADD CONSTRAINT jobs_ibfk_1 FOREIGN KEY (assigned_to) REFERENCES inspectors (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F8F4BD7827');
        $this->addSql('DROP TABLE inspector');
        $this->addSql('DROP TABLE job');
    }
}
