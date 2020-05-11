<?php
declare(strict_types=1);

namespace NsReisplanner\Infrastructure\ReadModel\Api;

use DateTime;
use NsReisplanner\Application\ReadModel\Departure;
use NsReisplanner\Application\ReadModel\Repository\DepartureRepository;
use NsReisplanner\Application\ReadModel\Train;
use NsReisplanner\Application\ReadModel\TrainCategory;
use NsReisplanner\Application\ReadModel\TrainOperator;
use NsReisplanner\Domain\DateRange;
use NsReisplanner\Infrastructure\Api\ApiClient;

final class ApiDepartureRepository implements DepartureRepository
{
    private ApiClient $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @inheritDoc
     */
    public function getByStationCodeForDateRange(string $stationCode, DateRange $dateRange): array
    {
        $departuresData = $this->apiClient->get(
            'https://gateway.apiportal.ns.nl/reisinformatie-api/api/v2/departures',
            [
                'dateTime' => $dateRange->getStart()->format(DateTime::RFC3339),
                'station' => $stationCode,
            ]
        );

        $result = [];
        foreach ($departuresData['payload']['departures'] as $departureData) {
            $departure = $this->mapToModel($departureData);

            if (!$dateRange->includes($departure->getPlannedDateTime())) {
                continue;
            }

            $result[] = $departure;
        }

        return $result;
    }

    /**
     * @param mixed[] $departureData
     */
    private function mapToModel(array $departureData): Departure
    {
        return new Departure(
            $departureData['direction'],
            $departureData['name'],
            $this->mapProductToTrainModel($departureData['product']),
            $departureData['plannedTrack'] ?? 'N.A.',
            DateTime::createFromFormat(
                DateTime::RFC3339,
                $departureData['plannedDateTime']
            ),
            DateTime::createFromFormat(
                DateTime::RFC3339,
                $departureData['actualDateTime'] ?? $departureData['plannedDateTime']
            ),
            $departureData['cancelled']
        );
    }

    /**
     * @param mixed[] $trainData
     */
    private function mapProductToTrainModel(array $trainData): Train
    {
        return new Train(
            $trainData['number'],
            new TrainCategory(
                $trainData['categoryCode'],
                $trainData['shortCategoryName'],
                $trainData['longCategoryName']
            ),
            new TrainOperator(
                $trainData['operatorCode'],
                $trainData['operatorName']
            )
        );
    }
}
