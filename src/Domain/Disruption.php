<?php
declare(strict_types=1);

namespace NsReisplanner\Domain;

use function array_key_exists;

final class Disruption
{
    private string $id;
    private string $title;
    private string $type;

    /**
     * @var AffectedStation[]
     */
    private array $affectedStations;

    /**
     * @param AffectedStation[] $affectedStations
     */
    public function __construct(string $id, string $title, string $type, array $affectedStations = [])
    {
        $this->id = $id;
        $this->title = $title;
        $this->type = $type;
        $this->affectedStations = $affectedStations;
    }

    /**
     * @param AffectedStation[] $affectedStations
     */
    public static function create(string $id, string $title, string $type, array $affectedStations): self
    {
        return new self($id, $title, $type, $affectedStations);
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

    /**
     * @return AffectedStation[]
     */
    public function getAffectedStations(): array
    {
        return $this->affectedStations;
    }

    public function hasAffectedStation(string $stationCode): bool
    {
        return array_key_exists($stationCode, $this->affectedStations);
    }

    public function addAffectedStation(AffectedStation $affectedStation): void
    {
        $this->affectedStations[$affectedStation->getStationCode()] = $affectedStation;
    }

    /**
     * @param AffectedStation[] $affectedStations
     */
    public function setAffectedStations(array $affectedStations): void
    {
        $this->affectedStations = $affectedStations;
    }
}
