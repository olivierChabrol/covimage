<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200602125749 extends AbstractMigration
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
        $this->addSql('ALTER TABLE image_unit ADD CONSTRAINT FK_2E81E6CD37C70060 FOREIGN KEY (stack_id) REFERENCES image_stack (id)');
        $this->addSql('ALTER TABLE image_stack ADD date DATE NOT NULL, ADD name VARCHAR(255) NOT NULL, ADD quantity INT NOT NULL, ADD token VARCHAR(15) NOT NULL, ADD analysed TINYINT(1) NOT NULL, ADD user VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE image_unit');
        $this->addSql('ALTER TABLE image_stack DROP date, DROP name, DROP quantity, DROP token, DROP analysed, DROP user');
    }
}
