<?php
declare(strict_types=1);

namespace NsReisplanner\Domain;

final class Station
{
    private string $code;
    private string $name;
    private string $countryCode;
    private string $uicCode;

    private function __construct(string $code, string $name, string $countryCode, string $uicCode)
    {
        $this->code = $code;
        $this->name = $name;
        $this->countryCode = $countryCode;
        $this->uicCode = $uicCode;
    }

    public static function create(string $code, string $name, string $countryCode, string $uicCode): self
    {
        return new self($code, $name, $countryCode, $uicCode);
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
