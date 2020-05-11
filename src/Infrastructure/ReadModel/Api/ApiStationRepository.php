<?php
declare(strict_types=1);

namespace NsReisplanner\Infrastructure\ReadModel\Api;

use NsReisplanner\Application\ReadModel\Repository\StationRepository;
use NsReisplanner\Application\ReadModel\Station;
use NsReisplanner\Infrastructure\Api\ApiClient;
use RuntimeException;

final class ApiStationRepository implements StationRepository
{
    private ApiClient $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function getByCode(string $stationCode): Station
    {
        throw new RuntimeException('Not implemented');
    }

    public function getByName(string $stationName): Station
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        $stationsData = $this->apiClient->get('https://gateway.apiportal.ns.nl/reisinformatie-api/api/v2/stations');

        $result = [];
        foreach ($stationsData['payload'] as $stationData) {
            $result[] = $this->mapToModel($stationData);
        }

        return $result;
    }

    /**
     * @param mixed[] $stationData
     */
    private function mapToModel(array $stationData): Station
    {
        return new Station(
            $stationData['code'],
            $stationData['namen']['lang'],
            $stationData['land'],
            $stationData['UICCode']
        );
    }
}
