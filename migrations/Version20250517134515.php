<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250517134515 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE appointment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, patient_id INTEGER DEFAULT NULL, doctor_id INTEGER DEFAULT NULL, time_slot_id INTEGER DEFAULT NULL, status VARCHAR(50) NOT NULL, CONSTRAINT FK_FE38F8446B899279 FOREIGN KEY (patient_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_FE38F84487F4FB17 FOREIGN KEY (doctor_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_FE38F844D62B0FA FOREIGN KEY (time_slot_id) REFERENCES time_slot (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_FE38F8446B899279 ON appointment (patient_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_FE38F84487F4FB17 ON appointment (doctor_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_FE38F844D62B0FA ON appointment (time_slot_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE notification (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, message CLOB NOT NULL, sent_at DATETIME NOT NULL, is_read BOOLEAN NOT NULL, CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BF5476CAA76ED395 ON notification (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE specialty (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description CLOB NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE test (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE time_slot (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, doctor_id INTEGER DEFAULT NULL, start_at DATETIME NOT NULL, end_at DATETIME NOT NULL, CONSTRAINT FK_1B3294A87F4FB17 FOREIGN KEY (doctor_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1B3294A87F4FB17 ON time_slot (doctor_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
            )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__project AS SELECT id, name, description, created_at FROM project
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE project
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE project (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            )
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO project (id, name, description, created_at) SELECT id, name, description, created_at FROM __temp__project
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__project
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__task AS SELECT id, project_id, title, description, status, due_date, created_at FROM task
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE task
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, project_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, status VARCHAR(20) NOT NULL, due_date DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            , created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , CONSTRAINT FK_527EDB25166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO task (id, project_id, title, description, status, due_date, created_at) SELECT id, project_id, title, description, status, due_date, created_at FROM __temp__task
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__task
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_527EDB25166D1F9C ON task (project_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__messenger_messages AS SELECT id, body, headers, queue_name, created_at, available_at, delivered_at FROM messenger_messages
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            )
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO messenger_messages (id, body, headers, queue_name, created_at, available_at, delivered_at) SELECT id, body, headers, queue_name, created_at, available_at, delivered_at FROM __temp__messenger_messages
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__messenger_messages
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE appointment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE notification
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE specialty
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE test
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE time_slot
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__messenger_messages AS SELECT id, body, headers, queue_name, created_at, available_at, delivered_at FROM messenger_messages
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL (DC2Type:datetime_immutable)
            , available_at DATETIME NOT NULL (DC2Type:datetime_immutable)
            , delivered_at DATETIME DEFAULT NULL (DC2Type:datetime_immutable)
            )
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO messenger_messages (id, body, headers, queue_name, created_at, available_at, delivered_at) SELECT id, body, headers, queue_name, created_at, available_at, delivered_at FROM __temp__messenger_messages
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__messenger_messages
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__project AS SELECT id, name, description, created_at FROM project
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE project
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE project (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, created_at DATETIME NOT NULL (DC2Type:datetime_immutable)
            )
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO project (id, name, description, created_at) SELECT id, name, description, created_at FROM __temp__project
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__project
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__task AS SELECT id, project_id, title, description, status, due_date, created_at FROM task
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE task
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, project_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, status VARCHAR(20) NOT NULL, due_date DATETIME DEFAULT NULL (DC2Type:datetime_immutable)
            , created_at DATETIME NOT NULL (DC2Type:datetime_immutable)
            , CONSTRAINT FK_527EDB25166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO task (id, project_id, title, description, status, due_date, created_at) SELECT id, project_id, title, description, status, due_date, created_at FROM __temp__task
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__task
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_527EDB25166D1F9C ON task (project_id)
        SQL);
    }
}
