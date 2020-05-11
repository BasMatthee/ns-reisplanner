<?php
declare(strict_types=1);

namespace NsReisplanner\Infrastructure\Persistence;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use NsReisplanner\Application\Exception\AggregateNotFoundException;
use NsReisplanner\Domain\AffectedStation;
use NsReisplanner\Domain\Disruption;
use NsReisplanner\Domain\DisruptionRepository;
use function sprintf;

final class DatabaseDisruptionRepository implements DisruptionRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getById(string $disruptionId): Disruption
    {
        $sql = <<<SQL
SELECT `id`,
       `title`,
       `type`
  FROM `disruptions`
 WHERE `id` = :id
SQL;

        $statement = $this->connection->prepare($sql);
        $statement->bindValue(':id', $disruptionId);
        $statement->execute();

        if (!$statement->rowCount()) {
            throw new AggregateNotFoundException(sprintf(
                'Disruption with ID %d could not be found',
                $disruptionId
            ));
        }

        $data = $statement->fetch(FetchMode::ASSOCIATIVE);

        $disruption = $this->mapDisruptionData($data);
        $disruption->setAffectedStations(
            $this->getAffectedStationsForDisruption($disruption->getId())
        );

        return $disruption;
    }

    public function add(Disruption $disruption): void
    {
        $sql = <<<SQL
INSERT INTO `disruptions` (
    `id`,
    `title`,
    `type`
) VALUES (
    :id,
    :title,
    :type
)
SQL;

        $statement = $this->connection->prepare($sql);
        $statement->bindValue(':id', $disruption->getId());
        $statement->bindValue(':title', $disruption->getTitle());
        $statement->bindValue(':type', $disruption->getType());

        $statement->execute();

        foreach ($disruption->getAffectedStations() as $affectedStation) {
            $this->addAffectedStation($affectedStation);
        }
    }

    public function update(Disruption $disruption): void
    {
        $sql = <<<SQL
INSERT IGNORE INTO `disruption_stations` (
    `disruption_id`,
    `station_code`,
    `start_date_time`,
    `end_date_time`
) VALUES (
    :disruptionId,
    :stationCode,
    :startDateTime,
    :endDateTime
)
SQL;

        $statement = $this->connection->prepare($sql);
        $statement->bindValue(':disruptionId', $disruption->getId());

        foreach ($disruption->getAffectedStations() as $affectedStation) {
            $statement->bindValue(':stationCode', $affectedStation->getStationCode());
            $statement->bindValue(':startDateTime', $affectedStation->getStartTime()->format(DateTime::RFC3339));
            $statement->bindValue(':endDateTime', $affectedStation->getEndTime()->format(DateTime::RFC3339));

            $statement->execute();
        }
    }

    private function addAffectedStation(AffectedStation $affectedStation): void
    {
        $sql = <<<SQL
INSERT INTO `disruption_stations` (
    `disruption_id`,
    `station`,
    `start_date_time`,
    `end_date_time`
) VALUES (
    :disruptionId,
    :station,
    :startDateTime,
    :endDateTime
)
SQL;

        $statement = $this->connection->prepare($sql);
        $statement->bindValue(':disruptionId', $affectedStation->getDisruptionId());
        $statement->bindValue(':station', $affectedStation->getStationCode());
        $statement->bindValue(':startDateTime', $affectedStation->getStartTime());
        $statement->bindValue(':endDateTime', $affectedStation->getEndTime());
        $statement->execute();
    }

    /**
     * @return AffectedStation[]
     */
    private function getAffectedStationsForDisruption(string $disruptionId): array
    {
        $sql = <<<SQL
SELECT `disruption_id`,
       `station_code`,
       `start_date_time`,
       `end_date_time`
  FROM `disruption_stations`
 WHERE `disruption_id` = :disruptionId
SQL;

        $statement = $this->connection->prepare($sql);
        $statement->bindValue(':disruptionId', $disruptionId);
        $statement->execute();

        $affectedStations = [];
        while ($data = $statement->fetch(FetchMode::ASSOCIATIVE)) {
            $affectedStations[] = $this->mapAffectedStationData($data);
        }

        return $affectedStations;
    }

    /**
     * @param mixed[] $data
     */
    private function mapDisruptionData(array $data): Disruption
    {
        return new Disruption(
            $data['id'],
            $data['title'],
            $data['type']
        );
    }

    /**
     * @param mixed[] $data
     */
    private function mapAffectedStationData(array $data): AffectedStation
    {
        return new AffectedStation(
            $data['disruption_id'],
            $data['station_code'],
            DateTime::createFromFormat('Y-m-d H:i:s', $data['start_date_time']),
            DateTime::createFromFormat('Y-m-d H:i:s', $data['end_date_time']),
        );
    }
}
