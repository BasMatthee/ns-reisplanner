<?php
declare(strict_types=1);

namespace NsReisplanner\Domain;

interface DisruptionRepository
{
    public function getById(string $disruptionId): Disruption;

    public function add(Disruption $disruption): void;

    public function update(Disruption $disruption): void;
}
