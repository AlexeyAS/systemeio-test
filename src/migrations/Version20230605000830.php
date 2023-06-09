<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230605000830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE "order_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sale_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tax_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "order" (id INT NOT NULL, tax_number VARCHAR(255) DEFAULT NULL, payment_processor VARCHAR(255) DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, timestamp INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE sale (id INT NOT NULL, code VARCHAR(255) NOT NULL, type VARCHAR(255) DEFAULT NULL, sale_price NUMERIC(10, 2) DEFAULT NULL, sale_percent NUMERIC(5, 2) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tax (id INT NOT NULL, country_code VARCHAR(2) NOT NULL, percent NUMERIC(5, 2) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO tax (id, country_code, percent) VALUES (1,'. "'DE'" . ', 19.00)');
        $this->addSql('INSERT INTO tax (id, country_code, percent) VALUES (2,'. "'IT'" . ', 22.00)');
        $this->addSql('INSERT INTO tax (id, country_code, percent) VALUES (3,'. "'GR'" . ', 24.00)');
        $this->addSql('ALTER sequence tax_id_seq start with 4');
        $this->addSql('INSERT INTO sale (id, code, type, sale_price, sale_percent) VALUES (1,'. "'SALE4','sale',NULL" . ', 4.00)');
        $this->addSql('INSERT INTO sale (id, code, type, sale_price, sale_percent) VALUES (2,'. "'GIFT10','gift'" . ', 10.00, NULL)');
        $this->addSql('ALTER sequence sale_id_seq start with 3');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE "order_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE sale_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tax_id_seq CASCADE');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('DROP TABLE sale');
        $this->addSql('DROP TABLE tax');
    }
}
