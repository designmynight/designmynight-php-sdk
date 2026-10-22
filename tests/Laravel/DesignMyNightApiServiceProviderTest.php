<?php

namespace DesignMyNight\Tests\Laravel;

use DesignMyNight\Client;
use DesignMyNight\Laravel\DesignMyNightApiServiceProvider;
use Orchestra\Testbench\TestCase;

class DesignMyNightApiServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [DesignMyNightApiServiceProvider::class];
    }

    public function test_registers_a_singleton_client_with_configured_credentials_and_base_uri(): void
    {
        config()->set('designmynight.user_id', 'id');
        config()->set('designmynight.api_key', 'key');
        config()->set('designmynight.base_url', 'https://api.example.test');
        config()->set('designmynight.api_version', 'v4');

        $client = $this->app->make(Client::class);

        self::assertInstanceOf(Client::class, $client);
        self::assertSame($client, $this->app->make(Client::class));
        self::assertSame('id:key', $client->getHttpClient()->getConfig('headers')['Authorization']);
        self::assertSame(
            'https://api.example.test/v4/',
            (string) $client->getHttpClient()->getConfig('base_uri')
        );
    }

    public function test_merges_default_package_config(): void
    {
        self::assertSame('v4', config('designmynight.api_version'));
        self::assertSame('https://api.designmynight.com', config('designmynight.base_url'));
    }
}
