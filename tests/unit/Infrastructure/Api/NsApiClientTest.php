<?php
declare(strict_types=1);

namespace NsReisplanner\Tests\Unit\Infrastructure\Api;

use GuzzleHttp\ClientInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use NsReisplanner\Infrastructure\Api\Exception\UnexpectedApiResponseException;
use NsReisplanner\Infrastructure\Api\NsApiClient;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
use function json_decode;
use const JSON_THROW_ON_ERROR;

class NsApiClientTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var ClientInterface|MockInterface
     */
    private MockInterface $client;

    /**
     * @var LoggerInterface|MockInterface
     */
    private MockInterface $logger;

    private NsApiClient $nsApiClient;

    protected function setUp(): void
    {
        $this->client = Mockery::mock(ClientInterface::class);
        $this->logger = Mockery::spy(LoggerInterface::class);

        $this->nsApiClient = new NsApiClient(
            $this->client,
            $this->logger,
            'DUMMY_API_KEY'
        );
    }

    public function testGetsResultFromGivenEndpoint(): void
    {
        $expectedResponse = '{"json":"response"}';
        $expectedResponseCode = 200;
        $endpoint = '/some/dummy/endpoint';

        $stream = Mockery::mock(StreamInterface::class);
        $stream->expects('getContents')->andReturn($expectedResponse);

        $response = Mockery::mock(ResponseInterface::class);
        $response->expects('getStatusCode')->andReturn($expectedResponseCode);
        $response->expects('getBody')->andReturn($stream);

        $this->client->expects('send')->andReturn($response);

        $result = $this->nsApiClient->get($endpoint, ['test' => 'data']);

        $this->assertEquals(json_decode($expectedResponse, true, 512, JSON_THROW_ON_ERROR), $result);
    }

    public function testThrowsExceptionOnUnexpectedStatusCode(): void
    {
        $expectedResponse = '{"json":"response"}';
        $expectedResponseCode = 403;
        $endpoint = '/some/dummy/endpoint';

        $this->expectException(UnexpectedApiResponseException::class);
        $this->expectExceptionMessage(sprintf(
            'Failed to retrieve %s?%s',
            $endpoint,
            'test=data',
        ));

        $stream = Mockery::mock(StreamInterface::class);
        $stream->expects('getContents')->andReturn($expectedResponse);

        $response = Mockery::mock(ResponseInterface::class);
        $response->expects('getStatusCode')->twice()->andReturn($expectedResponseCode);
        $response->expects('getBody')->andReturn($stream);

        $this->client->expects('send')->andReturn($response);

        $this->nsApiClient->get('/some/dummy/endpoint', ['test' => 'data']);

        $this->logger->shouldHaveReceived('error');
    }
}
