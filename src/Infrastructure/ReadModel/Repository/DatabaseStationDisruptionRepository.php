<?php
declare(strict_types=1);

namespace NsReisplanner\Infrastructure\ReadModel\Repository;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use NsReisplanner\Application\ReadModel\Repository\StationDisruptionRepository;
use NsReisplanner\Application\ReadModel\StationDisruption;
use NsReisplanner\Domain\DateRange;

final class DatabaseStationDisruptionRepository implements StationDisruptionRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @inheritDoc
     */
    public function findActiveByStationCode(string $stationCode): array
    {
        $sql = <<<SQL
SELECT `d`.`id`,
       `d`.`title`,
       `d`.`type`,
       `ds`.`start_date_time`,
       `ds`.`end_date_time`,
       `s`.`name`
  FROM `disruptions` AS `d`
 INNER JOIN `disruption_stations` AS `ds` ON (`ds`.`disruption_id` = `d`.`id`)
 INNER JOIN `stations` AS `s` ON (`s`.`code` = `ds`.`station_code`)
 WHERE NOW() BETWEEN `ds`.`start_date_time` AND `ds`.`end_date_time`
   AND `s`.`code` = :stationCode
SQL;

        $statement = $this->connection->prepare($sql);
        $statement->bindValue(':stationCode', $stationCode);
        $statement->execute();

        $result = [];
        while ($data = $statement->fetch(FetchMode::ASSOCIATIVE)) {
            $result[] = $this->mapToStationDisruptionModel($data);
        }

        return $result;
    }

    /**
     * @param mixed[] $data
     */
    private function mapToStationDisruptionModel(array $data): StationDisruption
    {
        return new StationDisruption(
            $data['id'],
            $data['title'],
            $data['type'],
            $data['station'],
            new DateRange(
                DateTime::createFromFormat('Y-m-d H:i:s', $data['start_date_time']),
                DateTime::createFromFormat('Y-m-d H:i:s', $data['end_date_time'])
            ),
        );
    }
}
