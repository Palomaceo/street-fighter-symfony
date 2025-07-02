<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250205131251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE character_skill (character_id INT NOT NULL, skill_id INT NOT NULL, INDEX IDX_A0FE03151136BE75 (character_id), INDEX IDX_A0FE03155585C142 (skill_id), PRIMARY KEY(character_id, skill_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE skill (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, damage INT DEFAULT NULL, heal INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE character_skill ADD CONSTRAINT FK_A0FE03151136BE75 FOREIGN KEY (character_id) REFERENCES `character` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE character_skill ADD CONSTRAINT FK_A0FE03155585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD email VARCHAR(255) NOT NULL, ADD pseudo VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE character_skill DROP FOREIGN KEY FK_A0FE03151136BE75');
        $this->addSql('ALTER TABLE character_skill DROP FOREIGN KEY FK_A0FE03155585C142');
        $this->addSql('DROP TABLE character_skill');
        $this->addSql('DROP TABLE skill');
        $this->addSql('ALTER TABLE user DROP email, DROP pseudo');
    }
}
