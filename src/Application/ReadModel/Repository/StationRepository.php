<?php
declare(strict_types=1);

namespace NsReisplanner\Application\ReadModel\Repository;

use NsReisplanner\Application\ReadModel\Station;

interface StationRepository
{
    public function getByCode(string $stationCode): Station;

    public function getByName(string $stationName): Station;

    /**
     * @return Station[]
     */
    public function findAll(): array;
}
