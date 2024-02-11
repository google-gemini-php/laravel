<?php

declare(strict_types=1);

namespace Gemini\Laravel\Facades;

use Gemini\Contracts\ResponseContract;
use Gemini\Laravel\Testing\GeminiFake;
use Illuminate\Support\Facades\Facade;

final class Gemini extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'gemini';
    }

    /**
     * @param  array<array-key, ResponseContract>  $responses
     */
    public static function fake(array $responses = []): GeminiFake
    {
        $fake = new GeminiFake($responses);
        self::swap($fake);

        return $fake;
    }
}
