<?php
declare(strict_types=1);

namespace NsReisplanner\Application\ReadModel;

use DateTime;

final class Departure
{
    private string $direction;
    private string $name;
    private Train $train;
    private string $plannedTrack;
    private DateTime $plannedDateTime;
    private DateTime $actualDateTime;
    private bool $cancelled;

    public function __construct(
        string $direction,
        string $name,
        Train $train,
        string $plannedTrack,
        DateTime $plannedDateTime,
        DateTime $actualDateTime,
        bool $cancelled
    ) {
        $this->direction = $direction;
        $this->name = $name;
        $this->train = $train;
        $this->plannedTrack = $plannedTrack;
        $this->plannedDateTime = $plannedDateTime;
        $this->actualDateTime = $actualDateTime;
        $this->cancelled = $cancelled;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTrain(): Train
    {
        return $this->train;
    }

    public function getPlannedTrack(): string
    {
        return $this->plannedTrack;
    }

    public function getPlannedDateTime(): DateTime
    {
        return $this->plannedDateTime;
    }

    public function getActualDateTime(): DateTime
    {
        return $this->actualDateTime;
    }

    public function isCancelled(): bool
    {
        return $this->cancelled;
    }
}
