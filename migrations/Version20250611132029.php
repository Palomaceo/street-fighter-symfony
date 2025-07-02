<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250611132029 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fight (id INT AUTO_INCREMENT NOT NULL, my_character_id INT NOT NULL, opponent_character_id INT NOT NULL, INDEX IDX_21AA445669C3E253 (my_character_id), INDEX IDX_21AA4456A1CEB025 (opponent_character_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fight ADD CONSTRAINT FK_21AA445669C3E253 FOREIGN KEY (my_character_id) REFERENCES `character` (id)');
        $this->addSql('ALTER TABLE fight ADD CONSTRAINT FK_21AA4456A1CEB025 FOREIGN KEY (opponent_character_id) REFERENCES `character` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fight DROP FOREIGN KEY FK_21AA445669C3E253');
        $this->addSql('ALTER TABLE fight DROP FOREIGN KEY FK_21AA4456A1CEB025');
        $this->addSql('DROP TABLE fight');
    }
}
