<?php
declare(strict_types=1);

namespace NsReisplanner\Infrastructure\ReadModel\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use NsReisplanner\Application\Exception\AggregateNotFoundException;
use NsReisplanner\Application\ReadModel\Repository\StationRepository;
use NsReisplanner\Application\ReadModel\Station;
use function sprintf;

final class DatabaseStationRepository implements StationRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @inheritDoc
     */
    public function getByCode(string $stationCode): Station
    {
        $sql = <<<SQL
SELECT `code`,
       `name`,
       `country_code`,
       `uic_code`
  FROM `stations`
 WHERE `code` = :code
SQL;

        $statement = $this->connection->prepare($sql);
        $statement->bindValue(':code', $stationCode);

        $statement->execute();

        if (!$statement->rowCount()) {
            throw new AggregateNotFoundException(
                sprintf(
                    'Station with station code "%s" could not be found',
                    $stationCode
                )
            );
        }

        $data = $statement->fetch(FetchMode::ASSOCIATIVE);

        return $this->mapDataToStation($data);
    }

    /**
     * @inheritDoc
     */
    public function getByName(string $stationName): Station
    {
        $sql = <<<SQL
SELECT `code`,
       `name`,
       `country_code`,
       `uic_code`
  FROM `stations`
 WHERE `name` = :name
SQL;

        $statement = $this->connection->prepare($sql);
        $statement->bindValue(':name', $stationName);

        $statement->execute();

        if (!$statement->rowCount()) {
            throw new AggregateNotFoundException(
                sprintf(
                    'Station with name "%s" could not be found',
                    $stationName
                )
            );
        }

        $data = $statement->fetch(FetchMode::ASSOCIATIVE);

        return $this->mapDataToStation($data);
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        $sql = <<<SQL
SELECT `code`,
       `name`,
       `country_code`,
       `uic_code`
  FROM `stations`
 ORDER BY `name`
SQL;

        $statement = $this->connection->prepare($sql);

        $statement->execute();

        $result = [];
        while ($data = $statement->fetch(FetchMode::ASSOCIATIVE)) {
            $result[] = $this->mapDataToStation($data);
        }

        return $result;
    }

    /**
     * @param mixed[] $data
     */
    private function mapDataToStation(array $data): Station
    {
        return new Station(
            $data['code'],
            $data['name'],
            $data['country_code'],
            $data['uic_code']
        );
    }
}
