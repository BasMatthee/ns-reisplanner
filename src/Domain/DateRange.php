<?php
declare(strict_types=1);

namespace NsReisplanner\Domain;

use DateTime;

final class DateRange
{
    private DateTime $startDate;
    private DateTime $endDate;

    public function __construct(DateTime $startDate, DateTime $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function getStart(): DateTime
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    public function includes(DateTime $otherDateTime): bool
    {
        if ($otherDateTime->getTimestamp() < $this->getStart()->getTimestamp()) {
            return false;
        }

        return $otherDateTime->getTimestamp() <= $this->getEndDate()->getTimestamp();
    }
}
