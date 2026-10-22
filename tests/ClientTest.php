<?php

namespace DesignMyNight\Tests;

use DesignMyNight\Api\Bookings;
use DesignMyNight\Api\CurrentUser;
use DesignMyNight\Api\Users;
use DesignMyNight\Api\Venues;
use DesignMyNight\Client;
use DesignMyNight\Exception\BadMethodCallException;
use DesignMyNight\Exception\InvalidArgumentException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private array $history = [];

    public function test_get_request_encodes_uri_and_returns_payload(): void
    {
        $client = $this->makeClient([
            new Response(200, [], '{"payload":{"available":true}}'),
        ]);

        $result = $client->venues()->bookingAvailability()->show('venue/1', [
            'date' => '2026-10-22',
            'num_people' => 4,
        ]);

        self::assertSame(['available' => true], $result);
        self::assertSame('GET', $this->history[0]['request']->getMethod());
        self::assertSame(
            'https://api.example.test/v4/venues/venue%2F1/booking-availability?date=2026-10-22&num_people=4',
            (string) $this->history[0]['request']->getUri()
        );
    }

    public function test_post_request_sends_raw_json_and_returns_payload(): void
    {
        $client = $this->makeClient([
            new Response(200, [], '{"payload":{"booking":{"booking_id":"b1"}}}'),
        ]);
        $booking = [
            'venue_id' => 'abc',
            'num_people' => 2,
            'source_details' => ['type' => 'phone'],
        ];

        $result = $client->bookings()->create($booking);
        $request = $this->history[0]['request'];

        self::assertSame(['booking' => ['booking_id' => 'b1']], $result);
        self::assertSame('POST', $request->getMethod());
        self::assertSame('https://api.example.test/v4/bookings', (string) $request->getUri());
        self::assertSame(json_encode($booking), $request->getBody()->__toString());
        self::assertFalse($request->hasHeader('Content-Type'));
    }

    public function test_request_sends_authorization_header_without_basic_auth(): void
    {
        $client = $this->makeClient([
            new Response(200, [], '{"payload":[]}'),
        ]);

        $client->venues()->bookingAvailability()->show('venue-1');

        self::assertSame('user-id:api-key', $this->history[0]['request']->getHeaderLine('Authorization'));
    }

    public function test_exposes_default_authorization_and_base_uri(): void
    {
        self::assertSame('u:k', Client::generateAuthorizationHeader('u', 'k'));
        self::assertSame('https://api.designmynight.com/v4/', Client::generateBaseUri());

        $client = Client::create('u', 'k');

        self::assertSame('u:k', $client->getHttpClient()->getConfig('headers')['Authorization']);
        self::assertSame(
            'https://api.designmynight.com/v4/',
            (string) $client->getHttpClient()->getConfig('base_uri')
        );
    }

    public function test_collins_api_credential_override_reuses_guzzle_config(): void
    {
        $client = $this->makeClient([
            new Response(200, [], '{"payload":{"booking":{"booking_id":"b1"}}}'),
        ]);
        $config = $client->getHttpClient()->getConfig();
        $handler = $config['handler'];
        $authorizationKey = Client::generateAuthorizationHeader('guided-user', 'guided-key');

        $config['headers']['Authorization'] = $authorizationKey;
        $config['auth'] = $authorizationKey;
        $client = new Client(new GuzzleClient($config));

        $result = $client->bookings()->create(['venue_id' => 'abc']);

        self::assertSame(['booking' => ['booking_id' => 'b1']], $result);
        self::assertSame($handler, $client->getHttpClient()->getConfig('handler'));
        self::assertSame($authorizationKey, $this->history[0]['request']->getHeaderLine('Authorization'));
    }

    public function test_404_throws_guzzle_client_exception(): void
    {
        $client = $this->makeClient([
            new Response(404, [], '{"payload":[]}'),
        ]);

        try {
            $client->venues()->bookingAvailability()->show('venue-1');
            self::fail('Expected a Guzzle client exception.');
        } catch (ClientException $exception) {
            self::assertSame(404, $exception->getResponse()->getStatusCode());
        }
    }

    public function test_500_throws_guzzle_server_exception(): void
    {
        $client = $this->makeClient([
            new Response(500, [], '{"payload":[]}'),
        ]);

        try {
            $client->venues()->bookingAvailability()->show('venue-1');
            self::fail('Expected a Guzzle server exception.');
        } catch (ServerException $exception) {
            self::assertSame(500, $exception->getResponse()->getStatusCode());
        }
    }

    public function test_api_and_magic_method_resolve_supported_apis(): void
    {
        $client = $this->makeClient([]);

        self::assertInstanceOf(CurrentUser::class, $client->api('me'));
        self::assertInstanceOf(CurrentUser::class, $client->api('currentUser'));
        self::assertInstanceOf(Users::class, $client->api('users'));
        self::assertInstanceOf(Venues::class, $client->venues());
        self::assertInstanceOf(Bookings::class, $client->bookings());
    }

    public function test_api_rejects_unknown_api(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->makeClient([])->api('nope');
    }

    public function test_magic_method_rejects_unknown_api(): void
    {
        $this->expectException(BadMethodCallException::class);

        $this->makeClient([])->nope();
    }

    private function makeClient(array $responses): Client
    {
        $stack = HandlerStack::create(new MockHandler($responses));
        $stack->push(Middleware::history($this->history));

        return new Client(new GuzzleClient([
            'base_uri' => 'https://api.example.test/v4/',
            'handler' => $stack,
            'headers' => ['Authorization' => 'user-id:api-key'],
            'auth' => 'user-id:api-key',
        ]));
    }
}
