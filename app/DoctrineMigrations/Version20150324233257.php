<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150324233257 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE ws_subscription DROP PRIMARY KEY");
        $this->addSql("ALTER TABLE ws_subscription ADD submission_id INT DEFAULT NULL, ADD finished_at DATETIME NOT NULL, CHANGE position state INT NOT NULL");
        $this->addSql("ALTER TABLE ws_subscription ADD CONSTRAINT FK_2A690037E1FD4933 FOREIGN KEY (submission_id) REFERENCES ws_submission (id) ON DELETE CASCADE");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_2A690037E1FD4933 ON ws_subscription (submission_id)");
        $this->addSql("ALTER TABLE ws_subscription ADD PRIMARY KEY (worm_id, queued_at)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE ws_subscription DROP FOREIGN KEY FK_2A690037E1FD4933");
        $this->addSql("DROP INDEX UNIQ_2A690037E1FD4933 ON ws_subscription");
        $this->addSql("ALTER TABLE ws_subscription DROP PRIMARY KEY");
        $this->addSql("ALTER TABLE ws_subscription DROP submission_id, DROP finished_at, CHANGE state position INT NOT NULL");
        $this->addSql("ALTER TABLE ws_subscription ADD PRIMARY KEY (worm_id, position)");
    }
}
