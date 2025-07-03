<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250703021930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Cards Table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('PRAGMA foreign_keys = ON');

        //-- active | blocked | expired
        //-- credit | prepaid
        $this->addSql("
            CREATE TABLE cards (
                id              INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id         INTEGER NOT NULL,
                account_id      INTEGER NOT NULL,
                card_number     TEXT    NOT NULL UNIQUE,
                holder_name     TEXT    NOT NULL,
                type            TEXT    NOT NULL DEFAULT 'prepaid',      
                limit_amount    NUMERIC(10,2) DEFAULT 0.00,
                balance         NUMERIC(10,2) NOT NULL DEFAULT 0.00,
                status          TEXT    NOT NULL DEFAULT 'active',       
                expiration_date DATE    NOT NULL,
                created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP,

                FOREIGN KEY (user_id)    REFERENCES users(id)     ON DELETE CASCADE,
                FOREIGN KEY (account_id) REFERENCES accounts(id)  ON DELETE CASCADE
            )");

        // Índices
        $this->addSql('CREATE INDEX idx_cards_user_id    ON cards (user_id)');
        $this->addSql('CREATE INDEX idx_cards_account_id ON cards (account_id)');
        $this->addSql('CREATE INDEX idx_cards_status     ON cards (status)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('PRAGMA foreign_keys = OFF');
        $this->addSql('DROP TABLE IF EXISTS cards');
    }
}
