<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250612092503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE expense (id INT AUTO_INCREMENT NOT NULL, period_id INT NOT NULL, expense_category_id INT NOT NULL, description VARCHAR(255) DEFAULT NULL, amount NUMERIC(10, 2) NOT NULL, expense_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', creation_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', modification_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2D3A8DA6EC8B7ADE (period_id), INDEX IDX_2D3A8DA66B2A3179 (expense_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE expense_category (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, category_name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, color VARCHAR(7) DEFAULT NULL, creation_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C02DDB38A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE income (id INT AUTO_INCREMENT NOT NULL, period_id INT NOT NULL, income_category_id INT NOT NULL, description VARCHAR(255) DEFAULT NULL, amount NUMERIC(10, 2) NOT NULL, income_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', creation_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', modification_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_3FA862D0EC8B7ADE (period_id), INDEX IDX_3FA862D053F8702F (income_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE income_category (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, category_name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, color VARCHAR(7) DEFAULT NULL, creation_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2F2D922FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE period (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, period_name VARCHAR(100) NOT NULL, start_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', end_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', period_type VARCHAR(255) NOT NULL, creation_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C5B81ECEA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE period_summary (id INT AUTO_INCREMENT NOT NULL, period_id INT NOT NULL, total_income NUMERIC(12, 2) NOT NULL, total_expenses NUMERIC(12, 2) NOT NULL, balance NUMERIC(12, 2) NOT NULL, calculation_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_35C1C33BEC8B7ADE (period_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, opption TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6EC8B7ADE FOREIGN KEY (period_id) REFERENCES period (id)');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA66B2A3179 FOREIGN KEY (expense_category_id) REFERENCES expense_category (id)');
        $this->addSql('ALTER TABLE expense_category ADD CONSTRAINT FK_C02DDB38A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE income ADD CONSTRAINT FK_3FA862D0EC8B7ADE FOREIGN KEY (period_id) REFERENCES period (id)');
        $this->addSql('ALTER TABLE income ADD CONSTRAINT FK_3FA862D053F8702F FOREIGN KEY (income_category_id) REFERENCES income_category (id)');
        $this->addSql('ALTER TABLE income_category ADD CONSTRAINT FK_2F2D922FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE period ADD CONSTRAINT FK_C5B81ECEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE period_summary ADD CONSTRAINT FK_35C1C33BEC8B7ADE FOREIGN KEY (period_id) REFERENCES period (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA6EC8B7ADE');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA66B2A3179');
        $this->addSql('ALTER TABLE expense_category DROP FOREIGN KEY FK_C02DDB38A76ED395');
        $this->addSql('ALTER TABLE income DROP FOREIGN KEY FK_3FA862D0EC8B7ADE');
        $this->addSql('ALTER TABLE income DROP FOREIGN KEY FK_3FA862D053F8702F');
        $this->addSql('ALTER TABLE income_category DROP FOREIGN KEY FK_2F2D922FA76ED395');
        $this->addSql('ALTER TABLE period DROP FOREIGN KEY FK_C5B81ECEA76ED395');
        $this->addSql('ALTER TABLE period_summary DROP FOREIGN KEY FK_35C1C33BEC8B7ADE');
        $this->addSql('DROP TABLE expense');
        $this->addSql('DROP TABLE expense_category');
        $this->addSql('DROP TABLE income');
        $this->addSql('DROP TABLE income_category');
        $this->addSql('DROP TABLE period');
        $this->addSql('DROP TABLE period_summary');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
