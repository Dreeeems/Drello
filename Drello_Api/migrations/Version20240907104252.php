<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240907104252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE projects (id INT AUTO_INCREMENT NOT NULL, team_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_5C93B3A4296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, status VARCHAR(255) DEFAULT NULL, INDEX IDX_527EDB25166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teams (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teams_user (teams_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_67DE2A5FD6365F12 (teams_id), INDEX IDX_67DE2A5FA76ED395 (user_id), PRIMARY KEY(teams_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(50) NOT NULL, username VARCHAR(50) NOT NULL, profile_pic VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A4296CD8AE FOREIGN KEY (team_id) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE teams_user ADD CONSTRAINT FK_67DE2A5FD6365F12 FOREIGN KEY (teams_id) REFERENCES teams (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE teams_user ADD CONSTRAINT FK_67DE2A5FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_admins ADD CONSTRAINT FK_D4FB52D6D6365F12 FOREIGN KEY (teams_id) REFERENCES teams (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_admins ADD CONSTRAINT FK_D4FB52D6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team_admins DROP FOREIGN KEY FK_D4FB52D6D6365F12');
        $this->addSql('ALTER TABLE team_admins DROP FOREIGN KEY FK_D4FB52D6A76ED395');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A4296CD8AE');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25166D1F9C');
        $this->addSql('ALTER TABLE teams_user DROP FOREIGN KEY FK_67DE2A5FD6365F12');
        $this->addSql('ALTER TABLE teams_user DROP FOREIGN KEY FK_67DE2A5FA76ED395');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE teams');
        $this->addSql('DROP TABLE teams_user');
        $this->addSql('DROP TABLE user');
    }
}
