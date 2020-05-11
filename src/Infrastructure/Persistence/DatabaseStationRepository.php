<?php
declare(strict_types=1);

namespace NsReisplanner\Infrastructure\Persistence;

use Doctrine\DBAL\Connection;
use NsReisplanner\Domain\Station;
use NsReisplanner\Domain\StationRepository;

final class DatabaseStationRepository implements StationRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function add(Station $station): void
    {
        $sql = <<<SQL
INSERT INTO `stations` (
    `code`,
    `name`,
    `country_code`,
    `uic_code`
) VALUES (
    :code,
    :name,
    :countryCode,
    :uicCode
)
SQL;

        $statement = $this->connection->prepare($sql);
        $statement->bindValue(':code', $station->getCode());
        $statement->bindValue(':name', $station->getName());
        $statement->bindValue(':countryCode', $station->getCountryCode());
        $statement->bindValue(':uicCode', $station->getUicCode());

        $statement->execute();
    }

    public function has(Station $station): bool
    {
        $sql = <<<SQL
SELECT `code`
  FROM `stations`
 WHERE `code` = :code
SQL;

        $statement = $this->connection->prepare($sql);
        $statement->bindValue(':code', $station->getCode());
        $statement->execute();

        return $statement->rowCount() > 0;
    }
}
