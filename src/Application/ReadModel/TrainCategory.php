<?php
declare(strict_types=1);

namespace NsReisplanner\Application\ReadModel;

final class TrainCategory
{
    private string $code;
    private string $shortName;
    private string $longName;

    public function __construct(string $code, string $shortName, string $longName)
    {
        $this->code = $code;
        $this->shortName = $shortName;
        $this->longName = $longName;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function getLongName(): string
    {
        return $this->longName;
    }
}
