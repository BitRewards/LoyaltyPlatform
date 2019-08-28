<?php

namespace App\Services\Fiat\Tickers;

use App\DTO\TickerData;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

abstract class AbstractTicker
{
    public $baseUrl;

    public function queryApi($endpoint, $method = 'GET', $params = [])
    {
        $client = new Client(['base_uri' => $this->baseUrl]);

        $data = null;

        try {
            /* @var Response $response*/
            $response = $client->request($method, $endpoint, ['params' => $params]);

            if (200 !== $response->getStatusCode()) {
                throw new InvalidTickerResponse('Invalid response status ('.$response->getStatusCode().').');
            }

            $data = $response->getBody()->getContents();
        } catch (InvalidTickerResponse $e) {
            \Log::error($e->getMessage(), [$endpoint, $method, $params, $response->getBody()->getContents()]);
        } catch (\Throwable $e) {
            \Log::error($e->getMessage(), [$endpoint, $method, $params, isset($response) ? $response->getBody()->getContents() : null]);
        }

        return $data;
    }

    abstract public function requestTickerData($coin);

    abstract public function convertResponse($response, $coin): TickerData;

    abstract public function isSupported(string $coin): bool;

    /**
     * @param $coin
     *
     * @return TickerData|null
     *
     * @throws InvalidTickerResponse
     */
    public function getTicker($coin)
    {
        $response = $this->requestTickerData($coin);

        $this->validateApiResponse($response);

        return $this->convertResponse($response, $coin);
    }

    public function validateApiResponse($response): void
    {
        if (empty($response)) {
            throw new InvalidTickerResponse('Empty response from ticker');
        }
    }
}
