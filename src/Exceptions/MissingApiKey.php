<?php

declare(strict_types=1);

namespace Gemini\Laravel\Exceptions;

use InvalidArgumentException;

/**
 * @internal
 */
class MissingApiKey extends InvalidArgumentException
{
    public static function create(): self
    {
        return new self(
            'The Gemini API Key is missing. Please publish the [gemini.php] configuration file and set the [api_key].'
        );
    }
}
