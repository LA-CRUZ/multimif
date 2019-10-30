<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191030105948 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE result (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, response_id INT NOT NULL, INDEX IDX_136AC113A76ED395 (user_id), INDEX IDX_136AC113FBF32840 (response_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC113A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC113FBF32840 FOREIGN KEY (response_id) REFERENCES reponse (id)');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE reponse CHANGE juste juste TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE result');
        $this->addSql('ALTER TABLE quiz CHANGE description description VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE reponse CHANGE juste juste TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
    }
}
