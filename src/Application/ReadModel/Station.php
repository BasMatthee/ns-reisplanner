<?php
declare(strict_types=1);

namespace NsReisplanner\Application\ReadModel;

final class Station
{
    private string $code;
    private string $name;
    private string $countryCode;
    private string $uicCode;

    public function __construct(string $code, string $name, string $countryCode, string $uicCode)
    {
        $this->code = $code;
        $this->name = $name;
        $this->countryCode = $countryCode;
        $this->uicCode = $uicCode;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getUicCode(): string
    {
        return $this->uicCode;
    }
}
