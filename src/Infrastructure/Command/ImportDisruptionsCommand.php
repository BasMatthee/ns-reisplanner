<?php
declare(strict_types=1);

namespace NsReisplanner\Infrastructure\Command;

use NsReisplanner\Application\Exception\AggregateNotFoundException;
use NsReisplanner\Application\ReadModel\Repository\DisruptionRepository as ExternalDisruptionRepository;
use NsReisplanner\Domain\AffectedStation;
use NsReisplanner\Domain\Disruption;
use NsReisplanner\Domain\DisruptionRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function count;
use function sprintf;

final class ImportDisruptionsCommand extends Command
{
    private ExternalDisruptionRepository $externalDisruptionRepository;
    private DisruptionRepository $disruptionRepository;

    public function __construct(
        ExternalDisruptionRepository $externalDisruptionRepository,
        DisruptionRepository $disruptionRepository
    ) {
        $this->externalDisruptionRepository = $externalDisruptionRepository;
        $this->disruptionRepository = $disruptionRepository;

        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->setName('ns-reisplanner:import-disruptions')
            ->setDescription('Import all available actual disruptions');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $externalDisruptions = $this->externalDisruptionRepository->findAll();

        $disruptionsCount = count($externalDisruptions);
        $added = 0;
        $updated = 0;

        $progress = new ProgressBar($output, $disruptionsCount);

        foreach ($externalDisruptions as $externalDisruption) {
            try {
                $disruption = $this->disruptionRepository->getById($externalDisruption->getId());

                foreach ($externalDisruption->getAffectedStations() as $externalAffectedStation) {
                    if ($disruption->hasAffectedStation($externalAffectedStation->getStationCode())) {
                        continue;
                    }

                    $affectedStation = new AffectedStation(
                        $externalAffectedStation->getDisruptionId(),
                        $externalAffectedStation->getStationCode(),
                        $externalAffectedStation->getStartTime(),
                        $externalAffectedStation->getEndTime()
                    );

                    $disruption->addAffectedStation($affectedStation);

                    $this->disruptionRepository->update($disruption);

                    $progress->advance();
                    $updated++;
                }
            } catch (AggregateNotFoundException $exception) {
                $disruption = Disruption::create(
                    $externalDisruption->getId(),
                    $externalDisruption->getTitle(),
                    $externalDisruption->getType(),
                    []
                );

                $this->disruptionRepository->add($disruption);

                $progress->advance();
                $added++;
            }
        }

        $progress->finish();

        $output->writeln('');
        $output->writeln(sprintf(
            '<info>%d actual disruptions processed, %d added, %d updated</info>',
            $disruptionsCount,
            $added,
            $updated
        ));

        return 0;
    }
}
