<?php
declare(strict_types=1);

namespace NsReisplanner\Infrastructure\ReadModel\Api;

use DateTime;
use NsReisplanner\Application\ReadModel\Disruption;
use NsReisplanner\Application\ReadModel\Repository\DisruptionRepository;
use NsReisplanner\Domain\AffectedStation;
use NsReisplanner\Infrastructure\Api\ApiClient;
use function array_key_exists;

final class ApiDisruptionRepository implements DisruptionRepository
{
    private ApiClient $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        $response = $this->apiClient->get('https://gateway.apiportal.ns.nl/reisinformatie-api/api/v2/disruptions', [
            'actual' => true,
        ]);

        $result = [];
        foreach ($response['payload'] as $disruption) {
            $result[] = $this->mapToModel($disruption);
        }

        return $result;
    }

    /**
     * @param mixed[] $disruptionData
     */
    private function mapToModel(array $disruptionData): Disruption
    {
        if (array_key_exists('verstoring', $disruptionData)) {
            $disruption = new Disruption(
                $disruptionData['id'],
                $disruptionData['verstoring']['type'],
                $disruptionData['titel'],
            );

            $this->mapAffectedStations($disruption, $disruptionData['verstoring']['trajecten']);

            return $disruption;
        }

        $disruption = new Disruption(
            $disruptionData['id'],
            $disruptionData['melding']['type'],
            $disruptionData['titel'],
        );

        return $disruption;
    }

    /**
     * @param mixed[] $routes
     */
    private function mapAffectedStations(Disruption $disruption, array $routes): void
    {
        $affectedStations = [];
        foreach ($routes as $route) {
            foreach ($route['stations'] as $station) {
                $affectedStations[] = AffectedStation::createNew(
                    $disruption->getId(),
                    $station,
                    DateTime::createFromFormat(DateTime::RFC3339, $route['begintijd']),
                    DateTime::createFromFormat(DateTime::RFC3339, $route['eindtijd'])
                );
            }
        }

        $disruption->setAffectedStations($affectedStations);
    }
}
