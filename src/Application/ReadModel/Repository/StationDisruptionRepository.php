<?php
declare(strict_types=1);

namespace NsReisplanner\Application\ReadModel\Repository;

use NsReisplanner\Application\ReadModel\StationDisruption;

interface StationDisruptionRepository
{
    /**
     * @return StationDisruption[]
     */
    public function findActiveByStationCode(string $stationCode): array;
}
