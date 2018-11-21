<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181022120442 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_quantity ADD discount_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product_quantity ADD CONSTRAINT FK_54437CA14C7C611F FOREIGN KEY (discount_id) REFERENCES discount (id)');
        $this->addSql('CREATE INDEX IDX_54437CA14C7C611F ON product_quantity (discount_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_quantity DROP FOREIGN KEY FK_54437CA14C7C611F');
        $this->addSql('DROP INDEX IDX_54437CA14C7C611F ON product_quantity');
        $this->addSql('ALTER TABLE product_quantity DROP discount_id');
    }
}
