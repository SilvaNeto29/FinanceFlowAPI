<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250426150722 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates users table';
    }

    public function up(Schema $schema): void
    {
        // Habilita suporte a foreign keys no SQLite
        $this->addSql('PRAGMA foreign_keys = ON');

        // Cria tabela users
        $this->addSql('
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(100) NOT NULL,
                username VARCHAR(50) NOT NULL,
                email VARCHAR(150) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        // Cria índice para email
        $this->addSql('CREATE UNIQUE INDEX idx_users_email ON users (email)');

        // Cria tabela refresh_tokens
        $this->addSql('
            CREATE TABLE IF NOT EXISTS refresh_tokens (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                token VARCHAR(512) NOT NULL,
                expires_at DATETIME NOT NULL,
                revoked BOOLEAN DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users (id) 
                    ON DELETE CASCADE
            )
        ');

        // Cria índice para user_id em refresh_tokens
        $this->addSql('CREATE INDEX idx_refresh_tokens_user ON refresh_tokens (user_id)');

        // Cria tabela token_blacklist
        $this->addSql('
            CREATE TABLE IF NOT EXISTS token_blacklist (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                token VARCHAR(512) NOT NULL,
                expires_at DATETIME NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME 
            )
        ');

        $this->addSql('CREATE INDEX idx_blacklist_token ON token_blacklist (token)');
        $this->addSql('CREATE INDEX idx_blacklist_expires ON token_blacklist (expires_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS token_blacklist');
        $this->addSql('DROP TABLE IF EXISTS refresh_tokens');
        $this->addSql('DROP TABLE IF EXISTS users');
    }
}
