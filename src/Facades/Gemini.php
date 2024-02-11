<?php

declare(strict_types=1);

namespace Gemini\Laravel\Facades;

use Gemini\Contracts\ResponseContract;
use Gemini\Enums\ModelType;
use Gemini\Laravel\Testing\GeminiFake;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Gemini\Resources\GenerativeModel generativeModel(ModelType $model)
 * @method static \Gemini\Resources\GenerativeModel geminiPro()
 * @method static \Gemini\Resources\GenerativeModel geminiProVision()
 * @method static \Gemini\Resources\GenerativeModel embeddingModel(ModelType $model = ModelType::EMBEDDING)
 * @method static \Gemini\Resources\Models models()
 * @method static \Gemini\Resources\ChatSession chat(ModelType $model = ModelType::GEMINI_PRO)
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
     * @param  array<array-key, ResponseContract>  $responses
     */
    public static function fake(array $responses = []): GeminiFake
    {
        $fake = new GeminiFake($responses);
        self::swap($fake);

        return $fake;
    }
}
