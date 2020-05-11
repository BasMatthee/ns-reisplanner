<?php
declare(strict_types=1);

namespace NsReisplanner\Application\ReadModel\Repository;

use NsReisplanner\Application\ReadModel\Disruption;

interface DisruptionRepository
{
    /**
     * @return Disruption[]
     */
    public function findAll(): array;
}
