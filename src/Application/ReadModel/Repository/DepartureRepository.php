<?php
declare(strict_types=1);

namespace NsReisplanner\Application\ReadModel\Repository;

use NsReisplanner\Application\ReadModel\Departure;
use NsReisplanner\Domain\DateRange;

interface DepartureRepository
{
    /**
     * @return Departure[]
     */
    public function getByStationCodeForDateRange(string $stationCode, DateRange $dateRange): array;
}
