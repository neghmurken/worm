<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150324233954 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE ws_subscription DROP PRIMARY KEY");
        $this->addSql("ALTER TABLE ws_subscription ADD position INT NOT NULL");
        $this->addSql("ALTER TABLE ws_subscription ADD PRIMARY KEY (worm_id, position)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE ws_subscription DROP PRIMARY KEY");
        $this->addSql("ALTER TABLE ws_subscription DROP position");
        $this->addSql("ALTER TABLE ws_subscription ADD PRIMARY KEY (worm_id, queued_at)");
    }
}
