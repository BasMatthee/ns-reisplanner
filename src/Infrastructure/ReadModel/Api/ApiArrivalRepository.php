<?php
declare(strict_types=1);

namespace NsReisplanner\Infrastructure\ReadModel\Api;

use DateTime;
use NsReisplanner\Application\ReadModel\Arrival;
use NsReisplanner\Application\ReadModel\Repository\ArrivalRepository;
use NsReisplanner\Application\ReadModel\Train;
use NsReisplanner\Application\ReadModel\TrainCategory;
use NsReisplanner\Application\ReadModel\TrainOperator;
use NsReisplanner\Domain\DateRange;
use NsReisplanner\Infrastructure\Api\ApiClient;

final class ApiArrivalRepository implements ArrivalRepository
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
        $arrivalsData = $this->apiClient->get(
            'https://gateway.apiportal.ns.nl/reisinformatie-api/api/v2/arrivals',
            [
                'dateTime' => $dateRange->getStart()->format(DateTime::RFC3339),
                'station' => $stationCode,
            ]
        );

        $result = [];
        foreach ($arrivalsData['payload']['arrivals'] as $arrivalData) {
            $arrival = $this->mapToModel($arrivalData);

            if (!$dateRange->includes($arrival->getPlannedDateTime())) {
                continue;
            }

            $result[] = $arrival;
        }

        return $result;
    }

    /**
     * @param mixed[] $arrivalData
     */
    private function mapToModel(array $arrivalData): Arrival
    {
        return new Arrival(
            $arrivalData['origin'],
            $arrivalData['name'],
            $arrivalData['plannedTrack'] ?? 'N.A.',
            $arrivalData['actualTrack'] ?? 'N.A.',
            $this->mapProductToTrainModel($arrivalData['product']),
            DateTime::createFromFormat(
                DateTime::RFC3339,
                $arrivalData['plannedDateTime']
            ),
            DateTime::createFromFormat(
                DateTime::RFC3339,
                $arrivalData['actualDateTime'] ?? $arrivalData['plannedDateTime']
            ),
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
