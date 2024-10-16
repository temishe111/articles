<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241016135255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE article (
            id SERIAL NOT NULL, 
            title VARCHAR(255) NOT NULL, 
            body VARCHAR(2049) NOT NULL, 
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
            PRIMARY KEY(id))'
        );
        $this->addSql('COMMENT ON COLUMN article.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN article.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(
            'CREATE TABLE article_tag (
            article_id INT NOT NULL, 
            tag_id INT NOT NULL, 
            PRIMARY KEY(article_id, tag_id))'
        );
        $this->addSql('CREATE INDEX IDX_article_tag_article_id ON article_tag (article_id)');
        $this->addSql('CREATE INDEX IDX_article_tag_tag_id ON article_tag (tag_id)');
        $this->addSql('CREATE TABLE tag (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql(
            'ALTER TABLE article_tag ADD CONSTRAINT FK_article_tag_article_id FOREIGN KEY (article_id) 
            REFERENCES article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql(
            'ALTER TABLE article_tag ADD CONSTRAINT FK_article_tag_tag_id 
    FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE article_tag DROP CONSTRAINT FK_article_tag_article_id');
        $this->addSql('ALTER TABLE article_tag DROP CONSTRAINT FK_article_tag_tag_id');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE article_tag');
        $this->addSql('DROP TABLE tag');
    }
}
