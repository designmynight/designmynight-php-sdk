<?php

namespace DesignMyNight;

use DesignMyNight\Api\AbstractApi;
use DesignMyNight\Api\CurrentUser;
use DesignMyNight\Api\Users;
use DesignMyNight\Exception\BadMethodCallException;
use DesignMyNight\Exception\InvalidArgumentException;
use GuzzleHttp\Client as GuzzleClient;

class Client
{
    const BASE_URI = 'https://api.designmynight.com';

    const API_VERSION = 'v4';

    public function __construct(GuzzleClient $client)
    {
        $this->client = $client;
    }

    public static function create($userId = null, $apiKey = null)
    {
        $client = new GuzzleClient([
          'base_uri' => self::generateBaseUri(),
          'headers' => [
            'Authorization' => self::generateAuthorizationHeader($userId, $apiKey)
          ],
          'auth' => self::generateAuthorizationHeader($userId, $apiKey)
        ]);

        return new self($client);
    }

    public static function generateAuthorizationHeader($userId = null, $apiKey = null)
    {
        return "{$userId}:{$apiKey}";
    }

    public static function generateBaseUri()
    {
        $baseUrl = self::BASE_URI;
        $apiVersion = self::API_VERSION;

        return "{$baseUrl}/{$apiVersion}/";
    }

    /**
     * @param string $name
     *
     * @throws InvalidArgumentException
     *
     * @return ApiInterface
     */
    public function api($name):AbstractApi
    {
        switch ($name) {
            case 'me':
            case 'currentUser':
                $api = new CurrentUser($this);
                break;

            case 'user':
            case 'users':
                $api = new Users($this);
                break;
            default:
                throw new InvalidArgumentException(sprintf('Undefined api instance called: "%s"', $name));
        }

        return $api;
    }

    public function getHttpClient():GuzzleClient
    {
        return $this->client;
    }

   /**
     * @param string $name
     *
     * @throws BadMethodCallException
     *
     * @return ApiInterface
     */
    public function __call($name, $args)
    {
        try {
            return $this->api($name);
        } catch (InvalidArgumentException $e) {
            throw new BadMethodCallException(sprintf('Undefined method called: "%s"', $name));
        }
    }
}