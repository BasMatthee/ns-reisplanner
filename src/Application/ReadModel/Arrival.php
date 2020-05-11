<?php
declare(strict_types=1);

namespace NsReisplanner\Application\ReadModel;

use DateTime;

final class Arrival
{
    private string $origin;
    private string $name;
    private string $plannedTrack;
    private string $actualTrack;
    private Train $train;
    private DateTime $plannedDateTime;
    private DateTime $actualDateTime;

    public function __construct(
        string $origin,
        string $name,
        string $plannedTrack,
        string $actualTrack,
        Train $train,
        DateTime $plannedDateTime,
        DateTime $actualDateTime
    ) {
        $this->origin = $origin;
        $this->name = $name;
        $this->plannedTrack = $plannedTrack;
        $this->actualTrack = $actualTrack;
        $this->train = $train;
        $this->plannedDateTime = $plannedDateTime;
        $this->actualDateTime = $actualDateTime;
    }

    public function getOrigin(): string
    {
        return $this->origin;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPlannedTrack(): string
    {
        return $this->plannedTrack;
    }

    public function getActualTrack(): string
    {
        return $this->actualTrack;
    }

    public function getTrain(): Train
    {
        return $this->train;
    }

    public function getPlannedDateTime(): DateTime
    {
        return $this->plannedDateTime;
    }

    public function getActualDateTime(): DateTime
    {
        return $this->actualDateTime;
    }
}
