<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200218203728 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE holidays_country (holidays_id INT NOT NULL, country_id INT NOT NULL, INDEX IDX_A474A9EE7C9675AB (holidays_id), INDEX IDX_A474A9EEF92F3E70 (country_id), PRIMARY KEY(holidays_id, country_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE holidays_country ADD CONSTRAINT FK_A474A9EE7C9675AB FOREIGN KEY (holidays_id) REFERENCES holidays (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE holidays_country ADD CONSTRAINT FK_A474A9EEF92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE holidays_country');
    }
}
