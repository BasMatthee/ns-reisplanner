<?php
declare(strict_types=1);

namespace NsReisplanner\Infrastructure\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200509072349 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
CREATE TABLE `stations` (
    `code` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `country_code` VARCHAR(255) NOT NULL,
    `uic_code` VARCHAR(255) NOT NULL,
    PRIMARY KEY(`code`))
    DEFAULT CHARACTER SET utf8mb4
    COLLATE `utf8mb4_unicode_ci`
    ENGINE = InnoDB;
SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<SQL
DROP SCHEMA `stations`
SQL
        );
    }
}
