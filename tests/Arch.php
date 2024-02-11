<?php

test('exceptions')
    ->expect('Gemini\Laravel\Exceptions')
    ->toUseNothing();

test('facades')
    ->expect('Gemini\Laravel\Facades\Gemini')
    ->toOnlyUse([
        'Illuminate\Support\Facades\Facade',
        'Gemini\Contracts\ResponseContract',
        'Gemini\Laravel\Testing\GeminiFake',
        'Gemini\Responses\StreamResponse',
    ]);

test('service providers')
    ->expect('Gemini\Laravel\ServiceProvider')
    ->toOnlyUse([
        'GuzzleHttp\Client',
        'Illuminate\Support\ServiceProvider',
        'Gemini\Laravel',
        'Gemini',
        'Illuminate\Contracts\Support\DeferrableProvider',

        // helpers...
        'config',
        'config_path',
    ]);
