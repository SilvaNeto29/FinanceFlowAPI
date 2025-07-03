<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250703020353 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Accounts Table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('PRAGMA foreign_keys = ON');

        //-- checking | savings | business
        // -- active | closed | blocked
        $this->addSql("
            CREATE TABLE accounts (
                id             INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id        INTEGER NOT NULL,
                bank_name      TEXT    NOT NULL,
                agency_number  TEXT    NOT NULL,
                account_number TEXT    NOT NULL,
                type           TEXT    NOT NULL DEFAULT 'checking', 
                balance        NUMERIC(10,2) NOT NULL DEFAULT 0.00,
                status         TEXT    NOT NULL DEFAULT 'active',
                created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE)"
        );

        // Índices
        $this->addSql('CREATE INDEX idx_accounts_user_id        ON accounts (user_id)');
        $this->addSql('CREATE INDEX idx_accounts_account_number ON accounts (account_number)');
        $this->addSql('CREATE INDEX idx_accounts_status         ON accounts (status)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('PRAGMA foreign_keys = OFF');
        $this->addSql('DROP TABLE IF EXISTS accounts');
    }
}
