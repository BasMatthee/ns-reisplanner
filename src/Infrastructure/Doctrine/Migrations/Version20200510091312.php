<?php
declare(strict_types=1);

namespace NsReisplanner\Infrastructure\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200510091312 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
CREATE TABLE `disruptions` (
    `id` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `type` VARCHAR(255) NOT NULL,
    PRIMARY KEY(`id`))
    DEFAULT CHARACTER SET utf8mb4
    COLLATE `utf8mb4_unicode_ci`
    ENGINE = InnoDB;
SQL
        );

        $this->addSql(<<<SQL
CREATE TABLE `disruption_stations` (
    `disruption_id` VARCHAR(255) NOT NULL,
    `station_code` VARCHAR(255) NOT NULL,
    `start_date_time` DATETIME NOT NULL,
    `end_date_time` DATETIME NOT NULL,
    PRIMARY KEY(`disruption_id`, `station_code`))
    DEFAULT CHARACTER SET utf8mb4
    COLLATE `utf8mb4_unicode_ci`
    ENGINE = InnoDB;
SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<SQL
DROP SCHEMA `disruptions`
SQL
        );

        $this->addSql(<<<SQL
DROP SCHEMA `disruption_stations`
SQL
        );
    }
}
