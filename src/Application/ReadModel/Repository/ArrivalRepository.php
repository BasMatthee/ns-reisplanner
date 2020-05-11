<?php
declare(strict_types=1);

namespace NsReisplanner\Application\ReadModel\Repository;

use NsReisplanner\Application\ReadModel\Arrival;
use NsReisplanner\Domain\DateRange;

interface ArrivalRepository
{
    /**
     * @return Arrival[]
     */
    public function getByStationCodeForDateRange(string $stationCode, DateRange $dateRange): array;
}
