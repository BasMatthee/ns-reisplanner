<?php
declare(strict_types=1);

namespace NsReisplanner\Infrastructure\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use NsReisplanner\Infrastructure\Api\Exception\UnexpectedApiResponseException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use function http_build_query;
use function json_decode;
use function json_encode;
use function sprintf;
use function strpos;
use const JSON_THROW_ON_ERROR;

final class NsApiClient implements ApiClient
{
    private ClientInterface $client;
    private LoggerInterface $logger;
    private string $apiKey;

    public function __construct(
        ClientInterface $client,
        LoggerInterface $logger,
        string $apiKey
    ) {
        $this->client = $client;
        $this->logger = $logger;
        $this->apiKey = $apiKey;
    }
    /**
     * @inheritDoc
     */
    public function get(string $endpoint, array $data = []): array
    {
        $endpoint = sprintf(
            '%s?%s',
            $endpoint,
            http_build_query($data)
        );

        $request = $this->createRequest('get', $endpoint);

        $response = $this->client->send($request);

        if (strpos((string) $response->getStatusCode(), '2') !== 0) {
            $this->logger->error(sprintf(
                'Unexpected %d response when calling %s: "%s"',
                $response->getStatusCode(),
                $endpoint,
                $response->getBody()->getContents()
            ));

            throw new UnexpectedApiResponseException(sprintf('Failed to retrieve %s', $endpoint));
        }

        return $this->parseResponse($response);
    }

    /**
     * @param mixed[] $data
     */
    private function createRequest(string $method, string $endpoint, array $data = []): RequestInterface
    {
        return new Request(
            $method,
            $endpoint,
            $this->getHeaders(),
            json_encode($data, JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @return string[]
     */
    private function getHeaders(): array
    {
        return [
            'Ocp-Apim-Subscription-Key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * @return mixed[]
     */
    private function parseResponse(ResponseInterface $response): array
    {
        $content = $response->getBody()->getContents();

        try {
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            throw new UnexpectedApiResponseException(sprintf(
                'Expected JSON response, got: "%s"',
                $content
            ));
        }

        return $data;
    }
}
