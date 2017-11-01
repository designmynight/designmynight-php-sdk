<?php
namespace DesignMyNight\Api;

use DesignMyNight\Client;
use DesignMyNight\Http\Response;

abstract class AbstractApi
{

    /**
     * @var Client
     */
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Send a GET request with query parameters.
     *
     * @param  string  $path           Request path.
     * @param  array   $parameters     GET parameters.
     * @param  array   $requestHeaders Request Headers.
     * @return array
     */
    protected function get($path, array $parameters = [], array $requestHeaders = []): array
    {
        $response = $this->client
            ->getHttpClient()
            ->request(
                'GET',
                $path,
                [
                    'query' => $parameters,
                    'headers' => $requestHeaders,
                ]
            );

        return Response::getContent($response);
    }
}
