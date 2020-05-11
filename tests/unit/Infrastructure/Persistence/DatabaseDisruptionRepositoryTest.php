<?php
declare(strict_types=1);

namespace NsReisplanner\Tests\integration\Infrastructure\Persistence;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use NsReisplanner\Infrastructure\Persistence\DatabaseDisruptionRepository;
use PHPUnit\Framework\TestCase;

class DatabaseDisruptionRepositoryTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var Connection|MockInterface
     */
    private MockInterface $connection;

    private DatabaseDisruptionRepository $disruptionRepository;

    protected function setUp(): void
    {
        $this->connection = Mockery::mock(Connection::class);

        $this->disruptionRepository = new DatabaseDisruptionRepository(
            $this->connection
        );
    }

    public function testGetsDisruptionById(): void
    {
        $expectedDisruptionId = 'some-id';
        $expectedDisruptionTitle = 'Some disruption';
        $expectedDisruptionType = 'WERKZAAMHEID';
        $expectedStationCode = 'AMF';
        $expectedStartDateTime = new DateTime('2020-01-01 12:00:00');
        $expectedEndDateTime =  new DateTime('2020-02-01 12:00:00');

        $disruptionStatement = Mockery::mock(Statement::class);
        $disruptionStatement->expects('bindValue')->with(':id', $expectedDisruptionId);
        $disruptionStatement->expects('execute');
        $disruptionStatement->expects('rowCount')->andReturn(1);
        $disruptionStatement->expects('fetch')->andReturn([
            'id' => $expectedDisruptionId,
            'title' => $expectedDisruptionTitle,
            'type' => $expectedDisruptionType,
        ]);

        $this->connection->expects('prepare')->andReturn($disruptionStatement);

        $affectedStationStatement = Mockery::mock(Statement::class);
        $affectedStationStatement->expects('bindValue')->with(':disruptionId', $expectedDisruptionId);
        $affectedStationStatement->expects('execute');
        $affectedStationStatement->expects('fetchAll')->andReturn([[
            'disruption_id' => $expectedDisruptionId,
            'station_code' => $expectedStationCode,
            'start_date_time' => $expectedStartDateTime->format('Y-m-d H:i:s'),
            'end_date_time' => $expectedEndDateTime->format('Y-m-d H:i:s'),
        ]]);

        $this->connection->expects('prepare')->andReturn($affectedStationStatement);

        $disruption = $this->disruptionRepository->getById('some-id');

        $this->assertEquals($expectedDisruptionId, $disruption->getId());
        $this->assertEquals($expectedDisruptionTitle, $disruption->getTitle());
        $this->assertEquals($expectedDisruptionType, $disruption->getType());
        $this->assertEquals($expectedStationCode, $disruption->getAffectedStations()[0]->getStationCode());
        $this->assertEquals(
            $expectedStartDateTime->getTimestamp(),
            $disruption->getAffectedStations()[0]->getStartTime()->getTimestamp()
        );
        $this->assertEquals(
            $expectedEndDateTime->getTimestamp(),
            $disruption->getAffectedStations()[0]->getEndTime()->getTimestamp()
        );
    }
}
