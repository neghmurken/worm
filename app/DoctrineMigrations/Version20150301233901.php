<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150301233901 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE ws_subscription (worm_id INT NOT NULL, position INT NOT NULL, user_id INT DEFAULT NULL, queued_at DATETIME NOT NULL, INDEX IDX_2A6900379E5F0C07 (worm_id), INDEX IDX_2A690037A76ED395 (user_id), PRIMARY KEY(worm_id, position)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE ws_worm (id INT AUTO_INCREMENT NOT NULL, author_user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, mode SMALLINT NOT NULL, time_limit INT NOT NULL, unique_queue TINYINT(1) NOT NULL, INDEX IDX_183F0407E2544CD6 (author_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE ws_subscription ADD CONSTRAINT FK_2A6900379E5F0C07 FOREIGN KEY (worm_id) REFERENCES ws_worm (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE ws_subscription ADD CONSTRAINT FK_2A690037A76ED395 FOREIGN KEY (user_id) REFERENCES ws_user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE ws_worm ADD CONSTRAINT FK_183F0407E2544CD6 FOREIGN KEY (author_user_id) REFERENCES ws_user (id) ON DELETE SET NULL");
        $this->addSql("ALTER TABLE ws_submission ADD worm_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE ws_submission ADD CONSTRAINT FK_48EE743D9E5F0C07 FOREIGN KEY (worm_id) REFERENCES ws_worm (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_48EE743D9E5F0C07 ON ws_submission (worm_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE ws_subscription DROP FOREIGN KEY FK_2A6900379E5F0C07");
        $this->addSql("ALTER TABLE ws_submission DROP FOREIGN KEY FK_48EE743D9E5F0C07");
        $this->addSql("DROP TABLE ws_subscription");
        $this->addSql("DROP TABLE ws_worm");
        $this->addSql("DROP INDEX IDX_48EE743D9E5F0C07 ON ws_submission");
        $this->addSql("ALTER TABLE ws_submission DROP worm_id");
    }
}
