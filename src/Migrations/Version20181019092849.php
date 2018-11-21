<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181019092849 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_order DROP INDEX UNIQ_17EB68C01AD5CDBF, ADD INDEX IDX_17EB68C01AD5CDBF (cart_id)');
        $this->addSql('ALTER TABLE user_order DROP INDEX UNIQ_17EB68C0A76ED395, ADD INDEX IDX_17EB68C0A76ED395 (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_order DROP INDEX IDX_17EB68C0A76ED395, ADD UNIQUE INDEX UNIQ_17EB68C0A76ED395 (user_id)');
        $this->addSql('ALTER TABLE user_order DROP INDEX IDX_17EB68C01AD5CDBF, ADD UNIQUE INDEX UNIQ_17EB68C01AD5CDBF (cart_id)');
    }
}
