<?php
declare(strict_types=1);

namespace NsReisplanner\Application\ReadModel;

use NsReisplanner\Domain\AffectedStation;
use Webmozart\Assert\Assert;

final class Disruption
{
    public const TYPE_PRIORITY_1 = 'MELDING_PRIO_1';
    public const TYPE_PRIORITY_2 = 'MELDING_PRIO_2';
    public const TYPE_PRIORITY_3 = 'MELDING_PRIO_3';
    public const TYPE_MALFUNCTION = 'STORING';
    public const TYPE_MAINTENANCE = 'WERKZAAMHEID';
    public const TYPE_EVENT = 'EVENEMENT';

    private string $id;
    private string $type;
    private string $title;

    /**
     * @var AffectedStation[]
     */
    private array $affectedStations = [];

    public function __construct(
        string $id,
        string $type,
        string $title
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->title = $title;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return AffectedStation[]
     */
    public function getAffectedStations(): array
    {
        return $this->affectedStations;
    }

    /**
     * @param AffectedStation[] $affectedStations
     */
    public function setAffectedStations(array $affectedStations): void
    {
        Assert::allIsInstanceOf($affectedStations, AffectedStation::class);

        $this->affectedStations = $affectedStations;
    }
}
