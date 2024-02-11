<?php

declare(strict_types=1);

namespace Gemini\Laravel;

use Gemini;
use Gemini\Client;
use Gemini\Contracts\ClientContract;
use Gemini\Laravel\Commands\InstallCommand;
use Gemini\Laravel\Exceptions\MissingApiKey;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use InvalidArgumentException;

final class ServiceProvider extends BaseServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ClientContract::class, static function (): Client {
            $apiKey = config('gemini.api_key');

            if (! is_string($apiKey)) {
                throw MissingApiKey::create();
            }

            $baseURL = config('gemini.base_url');
            if (isset($baseURL) && ! is_string($baseURL)) {
                throw new InvalidArgumentException('Invalid Gemini API base URL.');
            }

            $client = Gemini::factory()
                ->withApiKey(apiKey: $apiKey)
                ->withHttpClient(client: new GuzzleClient(['timeout' => config('gemini.request_timeout', 30)]));

            if (! empty($baseURL)) {
                $client->withBaseUrl(baseUrl: $baseURL);
            }

            return $client->make();
        });

        $this->app->alias(ClientContract::class, 'gemini');
        $this->app->alias(ClientContract::class, Client::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/gemini.php' => config_path('gemini.php'),
            ]);

            $this->commands([
                InstallCommand::class,
            ]);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            Client::class,
            ClientContract::class,
            'gemini',
        ];
    }
}
