<?php
declare(strict_types=1);

namespace NsReisplanner\Domain;

use DateTime;

final class AffectedStation
{
    private string $disruptionId;
    private string $stationCode;
    private DateTime $startTime;
    private DateTime $endTime;

    public function __construct(
        string $disruptionId,
        string $stationCode,
        DateTime $startTime,
        DateTime $endTime
    ) {
        $this->disruptionId = $disruptionId;
        $this->stationCode = $stationCode;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }

    public static function createNew(
        string $disruptionId,
        string $stationCode,
        DateTime $startTime,
        DateTime $endTime
    ): self {
        return new self($disruptionId, $stationCode, $startTime, $endTime);
    }

    public function getDisruptionId(): string
    {
        return $this->disruptionId;
    }

    public function getStationCode(): string
    {
        return $this->stationCode;
    }

    public function getStartTime(): DateTime
    {
        return $this->startTime;
    }

    public function getEndTime(): DateTime
    {
        return $this->endTime;
    }
}
