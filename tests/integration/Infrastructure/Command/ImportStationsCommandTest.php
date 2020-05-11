<?php
declare(strict_types=1);

namespace NsReisplanner\Tests\integration\Infrastructure\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use NsReisplanner\Application\Exception\AggregateNotFoundException;
use NsReisplanner\Application\ReadModel\Repository\StationRepository;
use NsReisplanner\Application\ReadModel\Repository\StationRepository as ExternalStationRepository;
use NsReisplanner\Application\ReadModel\Station;
use NsReisplanner\Infrastructure\Command\ImportStationsCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class ImportStationsCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    private StationRepository $stationRepository;

    /**
     * @var ExternalStationRepository|MockInterface
     */
    private MockInterface $externalStationRepository;

    private ImportStationsCommand $importStationsCommand;

    protected function setUp(): void
    {
        $this->externalStationRepository = Mockery::mock(ExternalStationRepository::class);

        self::bootKernel();
        self::$container->set('infrastructure.read_model.repository.station.api', $this->externalStationRepository);

        $this->importStationsCommand = self::$container->get('infrastructure.command.import_stations');
        $this->stationRepository = self::$container->get('infrastructure.read_model.repository.station');
    }

    public function testImportsStationsFromExternalSource(): void
    {
        $stations = [
            new Station('AMS', 'Amsterdam CS', 'NL', '111111111'),
            new Station('AMF', 'Amersfoort CS', 'NL', '222222222'),
        ];

        $input = new StringInput('');
        $output = new NullOutput();

        $this->externalStationRepository->shouldReceive('findAll')->andReturn($stations);

        $result = $this->importStationsCommand->execute($input, $output);

        $this->assertEquals(0, $result);
        $this->assertTrue($this->areStationsPersisted($stations));
    }

    /**
     * @param Station[] $expectedStations
     */
    private function areStationsPersisted(array $expectedStations): bool
    {
        foreach ($expectedStations as $expectedStation) {
            try {
                $this->stationRepository->getByCode($expectedStation->getCode());
            } catch (AggregateNotFoundException $exception) {
                return false;
            }
        }

        return true;
    }
}
