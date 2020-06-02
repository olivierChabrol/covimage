<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200602125049 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE image_unit (id INT AUTO_INCREMENT NOT NULL, stack_id INT NOT NULL, name VARCHAR(255) NOT NULL, size INT NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_2E81E6CD37C70060 (stack_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image_stack (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, name VARCHAR(255) NOT NULL, quantity INT NOT NULL, token VARCHAR(15) NOT NULL, analysed TINYINT(1) NOT NULL, user VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE image_unit ADD CONSTRAINT FK_2E81E6CD37C70060 FOREIGN KEY (stack_id) REFERENCES image_stack (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE image_unit DROP FOREIGN KEY FK_2E81E6CD37C70060');
        $this->addSql('DROP TABLE image_unit');
        $this->addSql('DROP TABLE image_stack');
    }
}
