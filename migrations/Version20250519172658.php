<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250519172658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__user AS SELECT id, email, password, roles, name FROM user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
            , name VARCHAR(255) NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO user (id, email, password, roles, name) SELECT id, email, password, roles, name FROM __temp__user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__user
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__user AS SELECT id, email, password, roles, name FROM user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
            , name VARCHAR(255) NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO user (id, email, password, roles, name) SELECT id, email, password, roles, name FROM __temp__user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__user
        SQL);
    }
}
