<?php
declare(strict_types=1);

namespace NsReisplanner\Infrastructure\Api;

interface ApiClient
{
    /**
     * @param mixed[] $data
     *
     * @return mixed[]
     */
    public function get(string $endpoint, array $data = []): array;
}
