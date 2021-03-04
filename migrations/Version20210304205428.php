<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210304205428 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP email_verified, DROP email_code, DROP email_code_date');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64991657DAE ON user (fullname)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8D93D64991657DAE ON user');
        $this->addSql('ALTER TABLE user ADD email_verified TINYINT(1) NOT NULL, ADD email_code VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD email_code_date DATETIME DEFAULT NULL');
    }
}
