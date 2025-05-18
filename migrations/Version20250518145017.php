<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250518145017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__time_slot AS SELECT id, doctor_id, start_at, end_at FROM time_slot
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE time_slot
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE time_slot (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, doctor_id INTEGER NOT NULL, start_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , end_at DATETIME NOT NULL, CONSTRAINT FK_1B3294A87F4FB17 FOREIGN KEY (doctor_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO time_slot (id, doctor_id, start_at, end_at) SELECT id, doctor_id, start_at, end_at FROM __temp__time_slot
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__time_slot
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1B3294A87F4FB17 ON time_slot (doctor_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__time_slot AS SELECT id, doctor_id, start_at, end_at FROM time_slot
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE time_slot
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE time_slot (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, doctor_id INTEGER DEFAULT NULL, start_at DATETIME NOT NULL, end_at DATETIME NOT NULL, CONSTRAINT FK_1B3294A87F4FB17 FOREIGN KEY (doctor_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO time_slot (id, doctor_id, start_at, end_at) SELECT id, doctor_id, start_at, end_at FROM __temp__time_slot
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__time_slot
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1B3294A87F4FB17 ON time_slot (doctor_id)
        SQL);
    }
}
