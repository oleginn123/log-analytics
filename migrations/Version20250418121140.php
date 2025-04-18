<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250418121140 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE log_entry CHANGE timestamp timestamp DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_serviceName ON log_entry (service_name)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_timestamp ON log_entry (timestamp)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_code ON log_entry (code)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX idx_serviceName ON log_entry
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_timestamp ON log_entry
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_code ON log_entry
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE log_entry CHANGE timestamp timestamp DATETIME NOT NULL
        SQL);
    }
}
