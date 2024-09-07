<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240907104130 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE team_admins (teams_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_D4FB52D6D6365F12 (teams_id), INDEX IDX_D4FB52D6A76ED395 (user_id), PRIMARY KEY(teams_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE team_admins ADD CONSTRAINT FK_D4FB52D6D6365F12 FOREIGN KEY (teams_id) REFERENCES teams (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_admins ADD CONSTRAINT FK_D4FB52D6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task CHANGE name name VARCHAR(255) NOT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team_admins DROP FOREIGN KEY FK_D4FB52D6D6365F12');
        $this->addSql('ALTER TABLE team_admins DROP FOREIGN KEY FK_D4FB52D6A76ED395');
        $this->addSql('DROP TABLE team_admins');
        $this->addSql('ALTER TABLE task CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE status status VARCHAR(255) NOT NULL');
    }
}
