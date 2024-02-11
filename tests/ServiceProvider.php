<?php

use Gemini\Client;
use Gemini\Contracts\ClientContract;
use Gemini\Laravel\Exceptions\MissingApiKey;
use Gemini\Laravel\ServiceProvider;
use Illuminate\Config\Repository;

it('binds the client on the container', function () {
    $app = app();

    $app->bind('config', fn () => new Repository([
        'gemini' => [
            'api_key' => 'test',
            'base_url' => 'test',
        ],
    ]));

    (new ServiceProvider($app))->register();

    expect($app->get(Client::class))->toBeInstanceOf(Client::class);
});

it('binds the client on the container as singleton', function () {
    $app = app();

    $app->bind('config', fn () => new Repository([
        'gemini' => [
            'api_key' => 'test',
        ],
    ]));

    (new ServiceProvider($app))->register();

    $client = $app->get(Client::class);

    expect($app->get(Client::class))->toBe($client);
});

it('requires an api key', function () {
    $app = app();

    $app->bind('config', fn () => new Repository([]));

    (new ServiceProvider($app))->register();
})->throws(
    MissingApiKey::class,
    'The Gemini API Key is missing. Please publish the [gemini.php] configuration file and set the [api_key].',
);

it('validates base url', function () {
    $app = app();

    $app->bind('config', fn () => new Repository([
        'gemini' => [
            'api_key' => 'test',
            'base_url' => 123,
        ],
    ]));

    (new ServiceProvider($app))->register();
})->throws(
    InvalidArgumentException::class,
    'Invalid Gemini API base URL.',
);

it('provides', function () {
    $app = app();

    $provides = (new ServiceProvider($app))->provides();

    expect($provides)->toBe([
        Client::class,
        ClientContract::class,
        'gemini',
    ]);
});
