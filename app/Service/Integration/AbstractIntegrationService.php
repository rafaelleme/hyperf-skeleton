<?php

namespace App\Service\Integration;

use App\Exception\BadException;
use App\Helper\StringHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

abstract class AbstractIntegrationService
{
    protected const TOKEN = '';

    protected string $url;

    private Client $client;

    private array $headers = [];

    public function make(?string $prefix = null): void
    {
        if (self::TOKEN) {
            $this->headers['Authorization'] = sprintf('Bearer %s', self::TOKEN);
        }

        $this->client = new Client([
            'base_uri' => sprintf('%s%s/', $this->url, $prefix),
            'headers' => $this->headers,
        ]);
    }

    /**
     * @throws GuzzleException
     * @throws BadException
     */
    public function get(string $url, array $options = []): mixed
    {
        try {
            $res = $this->client->get($url, $options);

            if ($res->getStatusCode() === 200) {
                return json_decode($res->getBody()->getContents(), true);
            }
        } catch (\Exception $e) {
            throw new BadException(
                StringHelper::getJson($e->getMessage()) ?? $e->getMessage()
            );
        }

        return [];
    }

    /**
     * @throws BadException|GuzzleException
     */
    public function post(string $url, array $payload = [], string $dataType = 'json', array $options = []): mixed
    {
        try {
            $options = array_merge($options, [
                $dataType => $payload,
            ]);
            $res = $this->client->post(
                $url,
                $options
            );

            if (in_array($res->getStatusCode(), [200, 201])) {
                return json_decode($res->getBody()->getContents(), true);
            }
        } catch (\Exception $e) {
            throw new BadException(
                StringHelper::getJson($e->getMessage()) ?? $e->getMessage()
            );
        }

        return [];
    }
}