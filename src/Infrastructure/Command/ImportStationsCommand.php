<?php
declare(strict_types=1);

namespace NsReisplanner\Infrastructure\Command;

use NsReisplanner\Application\ReadModel\Repository\StationRepository as ExternalStationRepository;
use NsReisplanner\Domain\Station;
use NsReisplanner\Domain\StationRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function count;
use function sprintf;

final class ImportStationsCommand extends Command
{
    private StationRepository $stationRepository;
    private ExternalStationRepository $externalStationRepository;

    public function __construct(
        StationRepository $stationRepository,
        ExternalStationRepository $externalStationRepository
    ) {
        $this->stationRepository = $stationRepository;
        $this->externalStationRepository = $externalStationRepository;

        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->setName('ns-reisplanner:import-stations')
            ->setDescription('Import all available stations');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $externalStations = $this->externalStationRepository->findAll();

        $stationCount = count($externalStations);
        $skipped = 0;
        $added = 0;

        $progress = new ProgressBar($output, $stationCount);

        foreach ($externalStations as $externalStation) {
            $station = Station::create(
                $externalStation->getCode(),
                $externalStation->getName(),
                $externalStation->getCountryCode(),
                $externalStation->getUicCode()
            );

            if ($this->stationRepository->has($station)) {
                $progress->advance();
                $skipped++;
                continue;
            }

            $this->stationRepository->add($station);

            $progress->advance();
            $added++;
        }

        $progress->finish();

        $output->writeln('');
        $output->writeln(sprintf(
            '<info>%d stations processed, %d added, %d skipped</info>',
            $stationCount,
            $added,
            $skipped
        ));

        return 0;
    }
}
