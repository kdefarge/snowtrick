<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210315073218 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick ADD featured_media_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91EE2532148 FOREIGN KEY (featured_media_id) REFERENCES media (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D8F0A91EE2532148 ON trick (featured_media_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91EE2532148');
        $this->addSql('DROP INDEX UNIQ_D8F0A91EE2532148 ON trick');
        $this->addSql('ALTER TABLE trick DROP featured_media_id');
    }
}
