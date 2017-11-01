<?php
namespace DesignMyNight\Laravel;

use DesignMyNight\Client;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class DesignMyNightApiServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $source = realpath(__DIR__ . '/../../config/designmynight.php');

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('designmynight.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('designmynight');
        }

        $this->mergeConfigFrom($source, 'designmynight');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('DesignMyNight\Client', function () {
            $authKey = $this->generateAuthorizationHeader();
            $client = new GuzzleClient([
                'base_uri' => $this->generateBaseUri(),
                'headers' => [
                    'Authorization' => $authKey,
                ],
                'auth' => $authKey,
            ]);

            return new Client($client);
        });
    }

    protected function generateAuthorizationHeader(): string
    {
        $userId = \Config::get('designmynight.user_id');
        $apiKey = \Config::get('designmynight.api_key');

        return "{$userId}:{$apiKey}";
    }

    protected function generateBaseUri(): string
    {
        $baseUrl = \Config::get('designmynight.base_url');
        $apiVersion = \Config::get('designmynight.api_version');

        return "{$baseUrl}/{$apiVersion}/";
    }
}
