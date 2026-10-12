<?php

declare(strict_types=1);

namespace Gemini\Laravel\Facades;

use BackedEnum;
use Gemini\Laravel\Testing\GeminiFake;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Gemini\Resources\GenerativeModel generativeModel(BackedEnum|string $model)
 * @method static \Gemini\Resources\EmbeddingModel embeddingModel(BackedEnum|string $model)
 * @method static \Gemini\Resources\Models models()
 * @method static \Gemini\Resources\ChatSession chat(BackedEnum|string $model)
 * @method static \Gemini\Resources\Files files()
 * @method static \Gemini\Resources\CachedContents cachedContents()
 * @method static \Gemini\Resources\FileSearchStores fileSearchStores()
 * @method static \Gemini\Resources\GenerativeModel geminiPro() Deprecated, use generativeModel() instead.
 * @method static \Gemini\Contracts\Resources\GenerativeModelContract geminiFlash() Deprecated, use generativeModel() instead.
 */
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
     * @param  array<array-key, mixed>  $responses
     */
    public static function fake(array $responses = []): GeminiFake
    {
        $fake = new GeminiFake($responses);
        self::swap($fake);

        return $fake;
    }
}
