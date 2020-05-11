<?php
declare(strict_types=1);

namespace NsReisplanner\Application\ReadModel;

use NsReisplanner\Domain\DateRange;

final class StationDisruption
{
    private string $id;
    private string $title;
    private string $type;
    private string $station;
    private DateRange $dateRange;

    public function __construct(string $id, string $title, string $type, string $station, DateRange $dateRange)
    {
        $this->id = $id;
        $this->title = $title;
        $this->type = $type;
        $this->station = $station;
        $this->dateRange = $dateRange;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getStation(): string
    {
        return $this->station;
    }

    public function getDateRange(): DateRange
    {
        return $this->dateRange;
    }
}
