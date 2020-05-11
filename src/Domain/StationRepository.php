<?php
declare(strict_types=1);

namespace NsReisplanner\Domain;

interface StationRepository
{
    public function add(Station $station): void;

    public function has(Station $station): bool;
}
