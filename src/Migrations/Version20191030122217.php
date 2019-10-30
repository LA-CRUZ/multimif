<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191030122217 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE result ADD question_id INT NOT NULL, ADD quiz_id INT NOT NULL');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC1131E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC113853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('CREATE INDEX IDX_136AC1131E27F6BF ON result (question_id)');
        $this->addSql('CREATE INDEX IDX_136AC113853CD175 ON result (quiz_id)');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE reponse CHANGE juste juste TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE quiz CHANGE description description VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE reponse CHANGE juste juste TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE result DROP FOREIGN KEY FK_136AC1131E27F6BF');
        $this->addSql('ALTER TABLE result DROP FOREIGN KEY FK_136AC113853CD175');
        $this->addSql('DROP INDEX IDX_136AC1131E27F6BF ON result');
        $this->addSql('DROP INDEX IDX_136AC113853CD175 ON result');
        $this->addSql('ALTER TABLE result DROP question_id, DROP quiz_id');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
    }
}
