<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230810224717 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_games (user_id INT NOT NULL, games_id INT NOT NULL, INDEX IDX_1DE1D069A76ED395 (user_id), INDEX IDX_1DE1D06997FFC673 (games_id), PRIMARY KEY(user_id, games_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_games ADD CONSTRAINT FK_1DE1D069A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_games ADD CONSTRAINT FK_1DE1D06997FFC673 FOREIGN KEY (games_id) REFERENCES games (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_games DROP FOREIGN KEY FK_1DE1D069A76ED395');
        $this->addSql('ALTER TABLE user_games DROP FOREIGN KEY FK_1DE1D06997FFC673');
        $this->addSql('DROP TABLE user_games');
    }
}
