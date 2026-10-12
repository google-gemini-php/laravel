<p align="center">
    <img src="https://raw.githubusercontent.com/google-gemini-php/laravel/main/art/example.png" width="600" alt="Google Gemini PHP for Laravel">
    <p align="center">
        <a href="https://packagist.org/packages/google-gemini-php/laravel"><img alt="Latest Version" src="https://img.shields.io/packagist/v/google-gemini-php/laravel"></a>
        <a href="https://packagist.org/packages/google-gemini-php/laravel"><img alt="License" src="https://img.shields.io/github/license/google-gemini-php/laravel"></a>
    </p>
</p>

------

**Gemini PHP** for Laravel is a community-maintained PHP API client for the [Gemini API](https://ai.google.dev/gemini-api/docs), with a facade, configuration and testing helpers for Laravel. It covers text, image, video, audio and document input, streaming, chats, structured output, function calling, built-in tools (Google Search, Google Maps, URL context, File Search, code execution), image and speech generation, thinking models, files, context caching, file search stores, embeddings and models, and ships with a fake client for testing.

- Fatih AYDIN [github.com/aydinfatih](https://github.com/aydinfatih)
- Vytautas Smilingis [github.com/Plytas](https://github.com/Plytas)

This package is a Laravel wrapper around [google-gemini-php/client](https://github.com/google-gemini-php/client). For more information, take a look at that repository.

## Table of Contents
- [Prerequisites](#prerequisites)
- [Setup](#setup)
  - [Installation](#installation)
  - [Setup your API key](#setup-your-api-key)
  - [Upgrade to 2.0](#upgrade-to-20)
- [Usage](#usage)
    - [Generating Content](#generating-content)
      - [Text-only Input](#text-only-input)
      - [Text-and-image Input](#text-and-image-input)
      - [Text-and-video Input](#text-and-video-input)
      - [Media Resolution](#media-resolution)
      - [Image Generation](#image-generation)
      - [Multi-turn Conversations (Chat)](#multi-turn-conversations-chat)
      - [Chat with Streaming](#chat-with-streaming)
      - [Stream Generate Content](#stream-generate-content)
      - [Structured Output](#structured-output)
      - [Function Calling](#function-calling)
      - [Code Execution](#code-execution)
      - [Grounding with Google Search](#grounding-with-google-search)
      - [Grounding with Google Maps](#grounding-with-google-maps)
      - [Grounding with File Search](#grounding-with-file-search)
      - [URL Context](#url-context)
      - [Server-side Tool Invocations](#server-side-tool-invocations)
      - [System Instructions](#system-instructions)
      - [Speech Generation](#speech-generation)
      - [Thinking Mode](#thinking-mode)
      - [Count Tokens](#count-tokens)
      - [Response Metadata](#response-metadata)
      - [Configuration](#configuration)
    - [File Management](#file-management)
      - [File Upload](#file-upload)
      - [List Files](#list-files)
      - [Get File Metadata](#get-file-metadata)
      - [Delete File](#delete-file)
    - [Cached Content](#cached-content)
      - [Create Cached Content](#create-cached-content)
      - [List Cached Content](#list-cached-content)
      - [Get Cached Content](#get-cached-content)
      - [Update Cached Content](#update-cached-content)
      - [Delete Cached Content](#delete-cached-content)
      - [Use Cached Content](#use-cached-content)
    - [File Search Stores](#file-search-stores)
      - [Create File Search Store](#create-file-search-store)
      - [Get File Search Store](#get-file-search-store)
      - [List File Search Stores](#list-file-search-stores)
      - [Delete File Search Store](#delete-file-search-store)
    - [File Search Documents](#file-search-documents)
      - [Upload File Search Document](#upload-file-search-document)
      - [Import File Search Document](#import-file-search-document)
      - [Get Operation](#get-operation)
      - [Get File Search Document](#get-file-search-document)
      - [List File Search Documents](#list-file-search-documents)
      - [Delete File Search Document](#delete-file-search-document)
    - [Embeddings](#embeddings)
    - [Models](#models)
        - [List Models](#list-models)
        - [Get Model](#get-model)
- [Troubleshooting](#troubleshooting)
    - [Error Handling](#error-handling)
    - [Empty Responses](#empty-responses)
    - [Timeout](#timeout)
- [Testing](#testing)


## Prerequisites
To complete this quickstart, make sure that your development environment meets the following requirements:

- Requires [PHP 8.2+](https://php.net/releases/)
- Requires [Laravel 9, 10, 11, 12 or 13](https://laravel.com/)

The features in this README need version 2.8 or later of `google-gemini-php/client`, which requires PHP 8.2. On PHP 8.1, Composer installs version 2.7 of the client.


## Setup

### Installation

First, install Gemini via the [Composer](https://getcomposer.org/) package manager:

```bash
composer require google-gemini-php/laravel
```

Next, execute the install command:

```bash
php artisan gemini:install
```

This creates a `config/gemini.php` configuration file and adds an empty `GEMINI_API_KEY` variable to your `.env` and `.env.example` files.

```
GEMINI_API_KEY=
```

You can also define the following environment variables:

```
GEMINI_BASE_URL=
GEMINI_REQUEST_TIMEOUT=
```

### Setup your API key
To use the Gemini API, you'll need an API key. If you don't already have one, create a key in Google AI Studio.

[Get an API key](https://aistudio.google.com/app/apikey)

### Upgrade to 2.0

Starting 2.0 release this package will work only with Gemini v1beta API ([see API versions](https://ai.google.dev/gemini-api/docs/api-versions)).

To update, run this command:

```bash
composer require google-gemini-php/laravel:^2.0
```

This release introduces support for new features:
* Structured output
* System instructions
* File uploads
* Function calling
* Code execution
* Grounding with Google Search
* Cached content
* Thinking model configuration
* Speech model configuration

`\Gemini\Enums\ModelType` enum has been deprecated and will be removed in next major version. Together with this `Gemini::geminiPro()` and `Gemini::geminiFlash()` methods have been deprecated as well.
We suggest using `Gemini::generativeModel()` method and pass in the model string directly. All methods that had previously accepted `ModelType` enum now accept a `BackedEnum`. We recommend implementing your own enum for convenience.

There may be other breaking changes not listed here. If you encounter any issues, please submit an issue or a pull request.

## Usage

Use the `Gemini` facade to send a prompt to a model:

```php
use Gemini\Enums\ModelVariation;
use Gemini\GeminiHelper;
use Gemini\Laravel\Facades\Gemini;

$result = Gemini::generativeModel(model: 'gemini-2.5-flash')->generateContent('Hello');
$result->text(); // Hello! How can I assist you today?

// Helper method usage
$result = Gemini::generativeModel(
    model: GeminiHelper::generateGeminiModel(
        variation: ModelVariation::FLASH,
        generation: 2.5,
    ), // models/gemini-2.5-flash
)->generateContent('Hello');
$result->text(); // Hello! How can I assist you today?
```

Model names change often. Check the [models documentation](https://ai.google.dev/gemini-api/docs/models) or [list the models](#list-models) available to your API key. The examples in this README use `gemini-2.5-flash`.

The API key, base URL and request timeout are read from `config/gemini.php`. The client is also bound in the container as `Gemini\Contracts\ClientContract`, so it can be injected:

```php
use Gemini\Contracts\ClientContract;

class SummaryController
{
    public function __invoke(ClientContract $gemini): string
    {
        return $gemini->generativeModel(model: 'gemini-2.5-flash')->generateContent('Hello')->text();
    }
}
```


### Generating Content

For a complete list of supported input formats and methods, see the [models documentation](https://ai.google.dev/gemini-api/docs/models).

#### Text-only Input
Generate a response from the model given an input message.

```php
use Gemini\Laravel\Facades\Gemini;

$result = Gemini::generativeModel(model: 'gemini-2.5-flash')->generateContent('Hello');

$result->text(); // Hello! How can I assist you today?
```

#### Text-and-image Input
Generate responses by providing both text prompts and images to the Gemini model.

```php
use Gemini\Data\Blob;
use Gemini\Enums\MimeType;
use Gemini\Laravel\Facades\Gemini;

$result = Gemini::generativeModel(model: 'gemini-2.5-flash')
    ->generateContent([
        'What is this picture?',
        new Blob(
            mimeType: MimeType::IMAGE_JPEG,
            data: base64_encode(
                file_get_contents('https://storage.googleapis.com/generativeai-downloads/images/scones.jpg')
            )
        )
    ]);

$result->text(); //  The picture shows a table with a white tablecloth. On the table are two cups of coffee, a bowl of blueberries, a silver spoon, and some flowers. There are also some blueberry scones on the table.
```

#### Text-and-video Input
Process video content and get AI-generated descriptions using the Gemini API with an [uploaded video file](#file-upload).

```php
use Gemini\Data\UploadedFile;
use Gemini\Enums\MimeType;
use Gemini\Laravel\Facades\Gemini;

$result = Gemini::generativeModel(model: 'gemini-2.5-flash')
    ->generateContent([
        'What is this video?',
        new UploadedFile(
            fileUri: $meta->uri, // the URI of the uploaded file, e.g. https://generativelanguage.googleapis.com/v1beta/files/abc-123
            mimeType: MimeType::VIDEO_MP4
        )
    ]);

$result->text(); //  The video shows...
```

#### Media Resolution
The tokenization quality of images, videos and documents can be set for the whole request with `GenerationConfig::$mediaResolution`, or for a single part with `Part::$mediaResolution`. A lower resolution uses fewer tokens, a higher one lets the model see more detail. `MEDIA_RESOLUTION_ULTRA_HIGH` is only available per part.

```php
use Gemini\Data\Blob;
use Gemini\Data\Content;
use Gemini\Data\Part;
use Gemini\Data\PartMediaResolution;
use Gemini\Enums\MimeType;
use Gemini\Enums\PartMediaResolutionLevel;
use Gemini\Enums\Role;
use Gemini\Laravel\Facades\Gemini;

$result = Gemini::generativeModel(model: 'gemini-2.5-flash')
    ->generateContent(new Content(
        parts: [
            new Part(
                inlineData: new Blob(mimeType: MimeType::IMAGE_PNG, data: base64_encode(file_get_contents('chart.png'))),
                mediaResolution: new PartMediaResolution(level: PartMediaResolutionLevel::MEDIA_RESOLUTION_HIGH),
            ),
            new Part(text: 'Read the values from this chart.'),
        ],
        role: Role::USER,
    ));

$result->text();
```

#### Image Generation
Generate images with a Gemini image model. Use `ImageConfig` to set the aspect ratio and the image size (`512`, `1K`, `2K` or `4K`, depending on the model).

```php
use Gemini\Data\GenerationConfig;
use Gemini\Data\ImageConfig;
use Gemini\Laravel\Facades\Gemini;

$imageConfig = new ImageConfig(aspectRatio: '16:9', imageSize: '2K');
$generationConfig = new GenerationConfig(imageConfig: $imageConfig);

$response = Gemini::generativeModel(model: 'gemini-2.5-flash-image')
    ->withGenerationConfig($generationConfig)
    ->generateContent('Draw a futuristic city');

// The response can contain text and image parts
foreach ($response->parts() as $part) {
    if ($part->inlineData !== null) {
        file_put_contents('image.png', base64_decode($part->inlineData->data));
    }
}
```

#### Multi-turn Conversations (Chat)
Using Gemini, you can build freeform conversations across multiple turns. The chat session keeps the history and sends it with every message.

```php
use Gemini\Data\Content;
use Gemini\Enums\Role;
use Gemini\Laravel\Facades\Gemini;

$chat = Gemini::generativeModel(model: 'gemini-2.5-flash')
    ->startChat(history: [
        Content::parse(part: 'The stories you write about what I have to say should be one line. Is that clear?'),
        Content::parse(part: 'Yes, I understand. The stories I write about your input should be one line long.', role: Role::MODEL)
    ]);

$response = $chat->sendMessage('Create a story set in a quiet village in 1600s France');
echo $response->text(); // Amidst rolling hills and winding cobblestone streets, the tranquil village of Beausoleil whispered tales of love, intrigue, and the magic of everyday life in 17th century France.

$response = $chat->sendMessage('Rewrite the same story in 1600s England');
echo $response->text(); // In the heart of England's lush countryside, amidst emerald fields and thatched-roof cottages, the village of Willowbrook unfolded a tapestry of love, mystery, and the enchantment of ordinary days in the 17th century.

$chat->history; // array<Content>
```

#### Chat with Streaming
You can also stream the response in a chat session. The history is automatically updated with the full response after the stream completes: text chunks are joined, while function calls, tool calls and thoughts are kept as separate parts, so they can be sent back to the model in the next turn.

```php
use Gemini\Laravel\Facades\Gemini;

$chat = Gemini::generativeModel(model: 'gemini-2.5-flash')->startChat();

$stream = $chat->streamSendMessage('Hello');

foreach ($stream as $response) {
    echo $response->text();
}
```


#### Stream Generate Content
By default, the model returns a response after completing the entire generation process. You can achieve faster interactions by not waiting for the entire result, and instead use streaming to handle partial results.

```php
use Gemini\Laravel\Facades\Gemini;

$stream = Gemini::generativeModel(model: 'gemini-2.5-flash')
    ->streamGenerateContent('Write a long story about a magic backpack.');

foreach ($stream as $response) {
    echo $response->text();
}
```

#### Structured Output
Gemini generates unstructured text by default, but some applications require structured text. For these use cases, you can constrain Gemini to respond with JSON, a structured data format suitable for automated processing. You can also constrain the model to respond with one of the options specified in an enum.

```php
use Gemini\Data\GenerationConfig;
use Gemini\Data\Schema;
use Gemini\Enums\DataType;
use Gemini\Enums\ResponseMimeType;
use Gemini\Laravel\Facades\Gemini;

$result = Gemini::generativeModel(model: 'gemini-2.5-flash')
    ->withGenerationConfig(
        generationConfig: new GenerationConfig(
            responseMimeType: ResponseMimeType::APPLICATION_JSON,
            responseSchema: new Schema(
                type: DataType::ARRAY,
                items: new Schema(
                    type: DataType::OBJECT,
                    properties: [
                        'recipe_name' => new Schema(type: DataType::STRING),
                        'cooking_time_in_minutes' => new Schema(type: DataType::INTEGER)
                    ],
                    required: ['recipe_name', 'cooking_time_in_minutes'],
                )
            )
        )
    )
    ->generateContent('List 5 popular cookie recipes with cooking time');

$result->json();

//[
//    {
//      +"cooking_time_in_minutes": 10,
//      +"recipe_name": "Chocolate Chip Cookies",
//    },
//    {
//      +"cooking_time_in_minutes": 12,
//      +"recipe_name": "Oatmeal Raisin Cookies",
//    },
//    {
//      +"cooking_time_in_minutes": 10,
//      +"recipe_name": "Peanut Butter Cookies",
//    },
//    {
//      +"cooking_time_in_minutes": 10,
//      +"recipe_name": "Snickerdoodles",
//    },
//    {
//      +"cooking_time_in_minutes": 12,
//      +"recipe_name": "Sugar Cookies",
//    },
//  ]

```

##### JSON Schema
Instead of a `Schema` object, you can pass a [JSON Schema](https://json-schema.org/) as an array with `responseJsonSchema`. In that case, leave `responseSchema` out.

```php
use Gemini\Data\GenerationConfig;
use Gemini\Enums\ResponseMimeType;
use Gemini\Laravel\Facades\Gemini;

$result = Gemini::generativeModel(model: 'gemini-2.5-flash')
    ->withGenerationConfig(new GenerationConfig(
        responseMimeType: ResponseMimeType::APPLICATION_JSON,
        responseJsonSchema: [
            'type' => 'object',
            'properties' => [
                'city' => ['type' => 'string'],
                'population' => ['type' => 'integer', 'minimum' => 0],
            ],
            'required' => ['city', 'population'],
        ],
    ))
    ->generateContent('Give me data about Istanbul.');

$result->json(associative: true); // ['city' => 'Istanbul', 'population' => 15655924]
```

An empty PHP array is encoded as a JSON list. Use `new \stdClass` where the schema needs an empty JSON object, e.g. `'properties' => new \stdClass`.

#### Function Calling
Gemini provides the ability to define and utilize custom functions that the model can call during conversations. This enables the model to perform specific actions or calculations through your defined functions.

```php
<?php

use Gemini\Data\Content;
use Gemini\Data\FunctionCall;
use Gemini\Data\FunctionDeclaration;
use Gemini\Data\FunctionResponse;
use Gemini\Data\Part;
use Gemini\Data\Schema;
use Gemini\Data\Tool;
use Gemini\Enums\DataType;
use Gemini\Enums\Role;
use Gemini\Laravel\Facades\Gemini;

function handleFunctionCall(FunctionCall $functionCall): Content
{
    if ($functionCall->name === 'addition') {
        return new Content(
            parts: [
                new Part(
                    functionResponse: new FunctionResponse(
                        name: 'addition',
                        response: ['answer' => $functionCall->args['number1'] + $functionCall->args['number2']],
                        id: $functionCall->id,
                    )
                )
            ],
            role: Role::USER
        );
    }

    //Handle other function calls
}

$chat = Gemini::generativeModel(model: 'gemini-2.5-flash')
    ->withTool(new Tool(
        functionDeclarations: [
            new FunctionDeclaration(
                name: 'addition',
                description: 'Performs addition',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [
                        'number1' => new Schema(
                            type: DataType::NUMBER,
                            description: 'First number'
                        ),
                        'number2' => new Schema(
                            type: DataType::NUMBER,
                            description: 'Second number'
                        ),
                    ],
                    required: ['number1', 'number2']
                )
            )
        ]
    ))
    ->startChat();

$response = $chat->sendMessage('What is 4 + 3?');

if ($response->parts()[0]->functionCall !== null) {
    $functionResponse = handleFunctionCall($response->parts()[0]->functionCall);

    $response = $chat->sendMessage($functionResponse);
}

echo $response->text(); // 4 + 3 = 7
```

Thinking models return a `thoughtSignature` with the function call part. The chat session keeps it in the history and sends it back automatically. If you manage the history yourself, send the model's parts back unchanged.

Function parameters can also be described with a JSON Schema array instead of a `Schema` object:

```php
use Gemini\Data\FunctionDeclaration;

new FunctionDeclaration(
    name: 'get_weather',
    description: 'Returns the weather for a city.',
    parametersJsonSchema: [
        'type' => 'object',
        'properties' => [
            'city' => ['type' => 'string'],
            'unit' => ['type' => 'string', 'enum' => ['celsius', 'fahrenheit']],
        ],
        'required' => ['city'],
    ],
);
```

##### Multimodal function responses
A function response can contain images or documents in `parts`, so the model can look at what your function returned.

```php
use Gemini\Data\Blob;
use Gemini\Data\Content;
use Gemini\Data\FunctionResponse;
use Gemini\Data\FunctionResponsePart;
use Gemini\Data\Part;
use Gemini\Enums\MimeType;
use Gemini\Enums\Role;

$response = $chat->sendMessage(new Content(
    parts: [
        new Part(functionResponse: new FunctionResponse(
            name: $functionCall->name,
            response: ['image' => 'product.png'],
            id: $functionCall->id,
            parts: [
                new FunctionResponsePart(inlineData: new Blob(mimeType: MimeType::IMAGE_PNG, data: base64_encode($png))),
            ],
        )),
    ],
    role: Role::USER,
));
```

#### Code Execution
Gemini models can generate and execute code automatically, and return the result to you. This is useful for tasks that require computation, data manipulation, or other programmatic operations.

```php
use Gemini\Data\CodeExecution;
use Gemini\Data\Tool;
use Gemini\Laravel\Facades\Gemini;

$response = Gemini::generativeModel(model: 'gemini-2.5-flash')
    ->withTool(new Tool(codeExecution: CodeExecution::from()))
    ->generateContent('What is the sum of the first 50 prime numbers? Generate and run code for the calculation, and make sure you get all 50.');

// Access the executed code and results
foreach ($response->parts() as $part) {
    if ($part->executableCode !== null) {
        echo "Language: " . $part->executableCode->language->value . "\n";
        echo "Code: " . $part->executableCode->code . "\n";
    }
    if ($part->codeExecutionResult !== null) {
        echo "Outcome: " . $part->codeExecutionResult->outcome->value . "\n";
        echo "Output: " . $part->codeExecutionResult->output . "\n";
    }
}
```

#### Grounding with Google Search
Grounding with Google Search connects the Gemini model to real-time web content and works with all available languages. This allows Gemini to provide more accurate answers and cite verifiable sources beyond its knowledge cutoff.

Use the `GoogleSearch` tool, which automatically handles search queries:

```php
use Gemini\Data\GoogleSearch;
use Gemini\Data\Tool;
use Gemini\Laravel\Facades\Gemini;

$response = Gemini::generativeModel(model: 'gemini-2.5-flash')
    ->withTool(new Tool(googleSearch: GoogleSearch::from()))
    ->generateContent('Who won the Euro 2024?');

echo $response->text();
// Spain won Euro 2024, defeating England 2-1 in the final.

// Access grounding metadata to see sources
$groundingMetadata = $response->candidates[0]->groundingMetadata;
if ($groundingMetadata !== null) {
    // Get the search queries that were executed
    foreach ($groundingMetadata->webSearchQueries ?? [] as $query) {
        echo "Search query: {$query}\n";
    }
    
    // Get the web sources
    foreach ($groundingMetadata->groundingChunks ?? [] as $chunk) {
        if ($chunk->web !== null) {
            echo "Source: {$chunk->web->title} - {$chunk->web->uri}\n";
        }
    }
    
    // Get grounding supports (links text segments to sources)
    foreach ($groundingMetadata->groundingSupports ?? [] as $support) {
        if ($support->segment !== null) {
            echo "Text segment: {$support->segment->text}\n";
            echo "Supported by chunks: " . implode(', ', $support->groundingChunkIndices ?? []) . "\n";
        }
    }
}
```

#### Grounding with Google Maps
Grounding with Google Maps allows the model to utilize real-world geographical data. This enables more precise location-based responses, such as finding nearby points of interest.

```php
use Gemini\Data\GoogleMaps;
use Gemini\Data\RetrievalConfig;
use Gemini\Data\Tool;
use Gemini\Data\ToolConfig;
use Gemini\Laravel\Facades\Gemini;

$tool = new Tool(
    googleMaps: new GoogleMaps(enableWidget: true)
);

$toolConfig = new ToolConfig(
    retrievalConfig: new RetrievalConfig(
        latitude: 40.758896,
        longitude: -73.985130
    )
);

$response = Gemini::generativeModel(model: 'gemini-2.5-flash')
    ->withTool($tool)
    ->withToolConfig($toolConfig)
    ->generateContent('Find coffee shops near me');

echo $response->text();
// (Model output referencing coffee shops)
```

#### Grounding with File Search
Grounding with File Search enables the model to retrieve and utilize information from documents in your [file search stores](#file-search-stores). This is useful for answering questions based on private or extensive document collections.

```php
use Gemini\Data\FileSearch;
use Gemini\Data\Tool;
use Gemini\Laravel\Facades\Gemini;

$tool = new Tool(
    fileSearch: new FileSearch(
        fileSearchStoreNames: ['fileSearchStores/my-search-store'],
        metadataFilter: 'author = "Robert Graves"'
    )
);

$response = Gemini::generativeModel(model: 'gemini-2.5-flash')
    ->withTool($tool)
    ->generateContent('Summarize the document about Greek myths by Robert Graves');

echo $response->text();
// (Model output summarizing the document)
```

#### URL Context
The URL context tool lets the model read the content of URLs in your prompt.

```php
use Gemini\Data\Tool;
use Gemini\Data\UrlContext;
use Gemini\Laravel\Facades\Gemini;

$response = Gemini::generativeModel(model: 'gemini-2.5-flash')
    ->withTool(new Tool(urlContext: new UrlContext))
    ->generateContent('Summarize https://www.php.net/releases/8.4/en.php in one sentence.');

echo $response->text();

// Check which URLs were retrieved
foreach ($response->candidates[0]->urlContextMetadata?->urlMetadata ?? [] as $urlMetadata) {
    echo "{$urlMetadata->retrievedUrl}: {$urlMetadata->urlRetrievalStatus?->value}\n"; // URL_RETRIEVAL_STATUS_SUCCESS
}
```

#### Server-side Tool Invocations
Built-in tools (Google Search, Google Maps, URL context and File Search) are executed by the API. Set `includeServerSideToolInvocations` to receive these calls and their results as `toolCall` and `toolResponse` parts. The chat session sends them back automatically in the next turn.

This requires a model that supports it, such as the Gemini 3 models. Other models reject the request with an `ErrorException`.

```php
use Gemini\Data\Tool;
use Gemini\Data\ToolConfig;
use Gemini\Data\UrlContext;
use Gemini\Laravel\Facades\Gemini;

$chat = Gemini::generativeModel(model: 'gemini-3.5-flash')
    ->withTool(new Tool(urlContext: new UrlContext))
    ->withToolConfig(new ToolConfig(includeServerSideToolInvocations: true))
    ->startChat();

$response = $chat->sendMessage('Summarize https://www.php.net/releases/8.4/en.php in one sentence.');

foreach ($response->parts() as $part) {
    if ($part->toolCall !== null) {
        echo "Tool call: {$part->toolCall->toolType?->value} " . json_encode($part->toolCall->args) . "\n";
    }
    if ($part->toolResponse !== null) {
        echo "Tool response: " . json_encode($part->toolResponse->response) . "\n";
    }
    if ($part->text !== null) {
        echo $part->text . "\n";
    }
}
```

Since the response contains several parts, read them with `parts()` instead of `text()`.

#### System Instructions
System instructions let you steer the behavior of the model based on your specific needs and use cases. You can set the role and personality of the model, define the format of responses, and provide goals and guardrails for model behavior.

```php
use Gemini\Data\Content;
use Gemini\Laravel\Facades\Gemini;

$response = Gemini::generativeModel(model: 'gemini-2.5-flash')
    ->withSystemInstruction(
        Content::parse('You are a helpful assistant that always responds in the style of a pirate. Use nautical terms and pirate slang in all your responses.')
    )
    ->generateContent('Tell me about PHP programming');

echo $response->text();
// Ahoy there, matey! Let me tell ye about this fine treasure called PHP programming...
```

You can also combine system instructions with other features:

```php
use Gemini\Data\Content;
use Gemini\Data\GenerationConfig;
use Gemini\Enums\ResponseMimeType;
use Gemini\Laravel\Facades\Gemini;

$response = Gemini::generativeModel(model: 'gemini-2.5-flash')
    ->withSystemInstruction(
        Content::parse('You are a JSON API. Always respond with valid JSON objects. Be concise.')
    )
    ->withGenerationConfig(
        new GenerationConfig(responseMimeType: ResponseMimeType::APPLICATION_JSON)
    )
    ->generateContent('Give me information about the Eiffel Tower');

print_r($response->json());
```

#### Speech Generation
Gemini allows generating [speech from a text](https://ai.google.dev/gemini-api/docs/speech-generation). To use that, make sure to use a model that supports this functionality. The model will output a base64 encoded audio string.

##### Single speaker

```php
use Gemini\Data\GenerationConfig;
use Gemini\Data\PrebuiltVoiceConfig;
use Gemini\Data\SpeechConfig;
use Gemini\Data\VoiceConfig;
use Gemini\Enums\ResponseModality;
use Gemini\Laravel\Facades\Gemini;

$response = Gemini::generativeModel('gemini-2.5-flash-preview-tts')->withGenerationConfig(
    generationConfig: new GenerationConfig(
        responseModalities: [ResponseModality::AUDIO],
        speechConfig: new SpeechConfig(
            voiceConfig: new VoiceConfig(
                new PrebuiltVoiceConfig(voiceName: 'Kore')
            ),
        )
    )
)->generateContent("Say: Hello world");

// The response contains base64 encoded audio
$audio = $response->parts()[0]->inlineData;
$audio->mimeType; // e.g. MimeType::AUDIO_L16_PCM_RATE_24000 (raw 16-bit PCM, 24 kHz, mono)
$audioData = base64_decode($audio->data);
```

The audio is raw PCM. To play it, add a WAV header or convert it, e.g. `ffmpeg -f s16le -ar 24000 -ac 1 -i audio.pcm audio.wav`.

##### Multi speaker

```php
use Gemini\Data\GenerationConfig;
use Gemini\Data\MultiSpeakerVoiceConfig;
use Gemini\Data\PrebuiltVoiceConfig;
use Gemini\Data\SpeakerVoiceConfig;
use Gemini\Data\SpeechConfig;
use Gemini\Data\VoiceConfig;
use Gemini\Enums\ResponseModality;
use Gemini\Laravel\Facades\Gemini;

$response = Gemini::generativeModel('gemini-2.5-flash-preview-tts')->withGenerationConfig(
    generationConfig: new GenerationConfig(
        responseModalities: [ResponseModality::AUDIO],
        speechConfig: new SpeechConfig(
            multiSpeakerVoiceConfig: new MultiSpeakerVoiceConfig([
                new SpeakerVoiceConfig(
                    speaker: 'Joe',
                    voiceConfig: new VoiceConfig(
                        new PrebuiltVoiceConfig('Kore'),
                    )
                ),
                new SpeakerVoiceConfig(
                    speaker: 'Jane',
                    voiceConfig: new VoiceConfig(
                        new PrebuiltVoiceConfig('Puck'),
                    )
                )
            ]),
            languageCode: 'en-GB'
        )
    )
)->generateContent("TTS the following conversation between Joe and Jane:\nJoe: How's it going today Jane?\nJane: Not too bad, how about you?");

// The response contains base64 encoded audio
$audioData = $response->parts()[0]->inlineData->data;
```

#### Thinking Mode
[Thinking models](https://ai.google.dev/gemini-api/docs/thinking) reason before they answer. Use `ThinkingConfig` to control how much they think and to receive summaries of their thoughts.

Gemini 2.5 models take a `thinkingBudget` (number of tokens), Gemini 3 models take a `thinkingLevel`.

```php
use Gemini\Data\GenerationConfig;
use Gemini\Data\ThinkingConfig;
use Gemini\Enums\ThinkingLevel;
use Gemini\Laravel\Facades\Gemini;

$response = Gemini::generativeModel(model: 'gemini-2.5-flash')
    ->withGenerationConfig(
        new GenerationConfig(
            thinkingConfig: new ThinkingConfig(
                includeThoughts: true,
                thinkingBudget: 1024 // Gemini 3: thinkingLevel: ThinkingLevel::LOW
            )
        )
    )
    ->generateContent('Solve this logic puzzle: If all Bloops are Razzies and all Razzies are Lazzies, are all Bloops definitely Lazzies?');

// Access the model's thoughts and final answer
foreach ($response->parts() as $part) {
    if ($part->thought === true) {
        // This part contains a summary of the model's thinking process
        echo "Model's thinking: " . $part->text . "\n\n";
    } else if ($part->text !== null) {
        // This is the final answer
        echo "Final answer: " . $part->text . "\n";
    }
}

echo $response->usageMetadata->thoughtsTokenCount; // tokens used for thinking
```

Thinking tokens count towards `maxOutputTokens`. If the limit is too low, the model can stop before writing an answer, see [Empty Responses](#empty-responses).

#### Count Tokens
When using long prompts, it might be useful to count tokens before sending any content to the model.

```php
use Gemini\Laravel\Facades\Gemini;

$response = Gemini::generativeModel(model: 'gemini-2.5-flash')
    ->countTokens('Write a story about a magic backpack.');

echo $response->totalTokens; // 9
```

#### Response Metadata
Besides the generated content, a response contains information about the request.

```php
use Gemini\Laravel\Facades\Gemini;

$response = Gemini::generativeModel(model: 'gemini-2.5-flash')->generateContent('Hello');

$response->responseId; // PqnIaobZOf3fxs0PkPSdiAk
$response->modelVersion; // gemini-2.5-flash
$response->modelStatus?->modelStage; // e.g. ModelStage::PREVIEW, when the API reports it

$response->usageMetadata->promptTokenCount; // 1
$response->usageMetadata->candidatesTokenCount; // 9
$response->usageMetadata->thoughtsTokenCount; // 20
$response->usageMetadata->totalTokenCount; // 30
$response->usageMetadata->serviceTier; // ServiceTier::STANDARD

$response->candidates[0]->finishReason; // FinishReason::STOP
$response->candidates[0]->finishMessage; // details on why the model stopped, when available
$response->candidates[0]->safetyRatings; // array<SafetyRating>
```

#### Configuration
Every prompt you send to the model includes parameter values that control how the model generates a response. The model can generate different results for different parameter values. Learn more about [model parameters](https://ai.google.dev/api/generate-content#generationconfig).

Also, you can use safety settings to adjust the likelihood of getting responses that may be considered harmful. Learn more about [safety settings](https://ai.google.dev/gemini-api/docs/safety-settings).

```php
use Gemini\Data\GenerationConfig;
use Gemini\Data\SafetySetting;
use Gemini\Enums\HarmBlockThreshold;
use Gemini\Enums\HarmCategory;
use Gemini\Laravel\Facades\Gemini;

$safetySettingDangerousContent = new SafetySetting(
    category: HarmCategory::HARM_CATEGORY_DANGEROUS_CONTENT,
    threshold: HarmBlockThreshold::BLOCK_ONLY_HIGH
);

$safetySettingHateSpeech = new SafetySetting(
    category: HarmCategory::HARM_CATEGORY_HATE_SPEECH,
    threshold: HarmBlockThreshold::BLOCK_ONLY_HIGH
);

$generationConfig = new GenerationConfig(
    stopSequences: [
        'Title',
    ],
    maxOutputTokens: 800,
    temperature: 1,
    topP: 0.8,
    topK: 10
);

$response = Gemini::generativeModel(model: 'gemini-2.5-flash')
    ->withSafetySetting($safetySettingDangerousContent)
    ->withSafetySetting($safetySettingHateSpeech)
    ->withGenerationConfig($generationConfig)
    ->generateContent('Write a story about a magic backpack.');
```

When using tools like `GoogleMaps`, you may also provide additional configuration via `ToolConfig`, such as `RetrievalConfig` for geographical context. See [Grounding with Google Maps](#grounding-with-google-maps).

### File Management

The File API lets you store up to 20 GB of files per project, with a per-file maximum size of 2 GB. Files are stored for 48 hours and can be accessed in API calls.

#### File Upload
To reference larger files and videos with various prompts, upload them to Gemini storage.

```php
use Gemini\Enums\FileState;
use Gemini\Enums\MimeType;
use Gemini\Laravel\Facades\Gemini;

$files = Gemini::files();
echo "Uploading\n";
$meta = $files->upload(
    filename: 'video.mp4',
    mimeType: MimeType::VIDEO_MP4,
    displayName: 'Video'
);
echo "Processing";
do {
    echo ".";
    sleep(2);
    $meta = $files->metadataGet($meta->uri);
} while (!$meta->state->complete());
echo "\n";

if ($meta->state == FileState::Failed) {
    die("Upload failed:\n" . json_encode($meta->toArray(), JSON_PRETTY_PRINT));
}

echo "Processing complete\n" . json_encode($meta->toArray(), JSON_PRETTY_PRINT);
echo "\n{$meta->uri}";
```

#### List Files
List all uploaded files in your project.

```php
use Gemini\Laravel\Facades\Gemini;

$response = Gemini::files()->list(pageSize: 10);

foreach ($response->files as $file) {
    echo "Name: {$file->name}\n";
    echo "Display Name: {$file->displayName}\n";
    echo "Size: {$file->sizeBytes} bytes\n";
    echo "MIME Type: {$file->mimeType}\n";
    echo "State: {$file->state->value}\n";
    echo "---\n";
}

// Get next page if available
if ($response->nextPageToken) {
    $nextPage = Gemini::files()->list(pageSize: 10, nextPageToken: $response->nextPageToken);
}
```

#### Get File Metadata
Retrieve metadata for a specific file. It accepts the file ID, the file name or the full URI.

```php
use Gemini\Laravel\Facades\Gemini;

$meta = Gemini::files()->metadataGet('abc-123');
// or use the name
$meta = Gemini::files()->metadataGet('files/abc-123');
// or use the full URI
$meta = Gemini::files()->metadataGet($file->uri);

echo "File: {$meta->displayName}\n";
echo "State: {$meta->state->value}\n";
echo "Size: {$meta->sizeBytes} bytes\n";
```

#### Delete File
Delete a file from Gemini storage. It accepts the file ID, the file name or the full URI.

```php
use Gemini\Laravel\Facades\Gemini;

Gemini::files()->delete('files/abc-123');
// or use the full URI
Gemini::files()->delete($file->uri);
```

### Cached Content

Context caching allows you to save and reuse precomputed input tokens for frequently used content. This reduces costs and latency for requests with large amounts of shared context. The content has to meet a [minimum token count](https://ai.google.dev/gemini-api/docs/caching) to be cached.

#### Create Cached Content
Cache content that you'll reuse across multiple requests.

```php
use Gemini\Data\Content;
use Gemini\Laravel\Facades\Gemini;

$cachedContent = Gemini::cachedContents()->create(
    model: 'gemini-2.5-flash',
    systemInstruction: Content::parse('You are an expert PHP developer.'),
    parts: [
        'This is a large codebase...',
        'File 1 contents...',
        'File 2 contents...'
    ],
    ttl: '3600s', // Cache for 1 hour
    displayName: 'PHP Codebase Cache'
);

echo "Cached content created: {$cachedContent->name}\n";
```

#### List Cached Content
List all cached content in your project.

```php
use Gemini\Laravel\Facades\Gemini;

$response = Gemini::cachedContents()->list(pageSize: 10);

foreach ($response->cachedContents as $cached) {
    echo "Name: {$cached->name}\n";
    echo "Display Name: {$cached->displayName}\n";
    echo "Model: {$cached->model}\n";
    echo "Expires: {$cached->expireTime}\n";
    echo "---\n";
}
```

#### Get Cached Content
Retrieve a specific cached content by name.

```php
use Gemini\Laravel\Facades\Gemini;

$cached = Gemini::cachedContents()->retrieve('cachedContents/abc123');

echo "Model: {$cached->model}\n";
echo "Created: {$cached->createTime}\n";
echo "Expires: {$cached->expireTime}\n";
```

#### Update Cached Content
Update the expiration time of cached content.

```php
use Gemini\Laravel\Facades\Gemini;

// Set a new TTL, counted from now
$updated = Gemini::cachedContents()->update(
    name: 'cachedContents/abc123',
    ttl: '7200s' // Expires in 2 hours
);

// Or set absolute expiration time
$updated = Gemini::cachedContents()->update(
    name: 'cachedContents/abc123',
    expireTime: '2030-12-31T23:59:59Z'
);
```

#### Delete Cached Content
Delete cached content when no longer needed.

```php
use Gemini\Laravel\Facades\Gemini;

Gemini::cachedContents()->delete('cachedContents/abc123');
```

#### Use Cached Content
Use cached content in your requests to save tokens and reduce latency.

```php
use Gemini\Laravel\Facades\Gemini;

$response = Gemini::generativeModel(model: 'gemini-2.5-flash')
    ->withCachedContent('cachedContents/abc123')
    ->generateContent('Explain the main function in this codebase');

echo $response->text();

// Check token usage
echo "Cached tokens used: {$response->usageMetadata->cachedContentTokenCount}\n";
echo "Prompt tokens used (including cached tokens): {$response->usageMetadata->promptTokenCount}\n";
```

### File Search Stores

A file search store holds documents that the model can search with the [File Search tool](#grounding-with-file-search).

#### Create File Search Store
Create a file search store.

```php
use Gemini\Laravel\Facades\Gemini;

$fileSearchStore = Gemini::fileSearchStores()->create(
    displayName: 'My Search Store',
);

echo "File search store created: {$fileSearchStore->name}\n"; // fileSearchStores/my-search-store-abc123
```

#### Get File Search Store
Get a specific file search store by name.

```php
use Gemini\Laravel\Facades\Gemini;

$fileSearchStore = Gemini::fileSearchStores()->get('fileSearchStores/my-search-store');

echo "Name: {$fileSearchStore->name}\n";
echo "Display Name: {$fileSearchStore->displayName}\n";
```

#### List File Search Stores
List all file search stores.

```php
use Gemini\Laravel\Facades\Gemini;

$response = Gemini::fileSearchStores()->list(pageSize: 10);

foreach ($response->fileSearchStores as $fileSearchStore) {
    echo "Name: {$fileSearchStore->name}\n";
    echo "Display Name: {$fileSearchStore->displayName}\n";
    echo "--- \n";
}
```

#### Delete File Search Store
Delete a file search store by name. Set `force` to also delete the documents in it.

```php
use Gemini\Laravel\Facades\Gemini;

Gemini::fileSearchStores()->delete('fileSearchStores/my-search-store', force: true);
```

### File Search Documents

#### Upload File Search Document
Upload a local file directly to a file search store. Custom metadata can be used to filter documents with `metadataFilter` in the [File Search tool](#grounding-with-file-search). Values can be strings, numbers or lists of strings.

```php
use Gemini\Enums\MimeType;
use Gemini\Laravel\Facades\Gemini;

$operation = Gemini::fileSearchStores()->upload(
    storeName: 'fileSearchStores/my-search-store',
    filename: 'document.pdf',
    mimeType: MimeType::APPLICATION_PDF,
    displayName: 'Greek Myths',
    customMetadata: [
        'author' => 'Robert Graves',
        'year' => 1955,
        'tags' => ['mythology', 'greece'],
    ],
);

echo "Upload operation: {$operation->name}\n";
```

#### Import File Search Document
Import a file that was uploaded with the [File API](#file-upload) into a file search store.

```php
use Gemini\Laravel\Facades\Gemini;

$file = Gemini::files()->upload(filename: 'document.pdf');

$operation = Gemini::fileSearchStores()->importFile(
    storeName: 'fileSearchStores/my-search-store',
    fileName: $file->name,
    customMetadata: ['author' => 'Robert Graves'],
);
```

#### Get Operation
Uploading and importing are long-running operations. Poll the operation until it is done.

```php
use Gemini\Laravel\Facades\Gemini;

while ($operation->done === false) {
    sleep(2);
    $operation = Gemini::fileSearchStores()->getOperation($operation->name);
}

if ($operation->error !== null) {
    die('Failed: ' . json_encode($operation->error));
}

echo "Document added\n";
```

#### Get File Search Document
Get a specific file search document by name.

```php
use Gemini\Laravel\Facades\Gemini;

$fileSearchDocument = Gemini::fileSearchStores()->getDocument('fileSearchStores/my-search-store/documents/my-document');

echo "Name: {$fileSearchDocument->name}\n";
echo "Display Name: {$fileSearchDocument->displayName}\n";
echo "State: {$fileSearchDocument->state?->value}\n";
```

#### List File Search Documents
List all file search documents within a store.

```php
use Gemini\Laravel\Facades\Gemini;

$response = Gemini::fileSearchStores()->listDocuments(storeName: 'fileSearchStores/my-search-store', pageSize: 10);

foreach ($response->documents as $fileSearchDocument) {
    echo "Name: {$fileSearchDocument->name}\n";
    echo "Display Name: {$fileSearchDocument->displayName}\n";
    echo "Create Time: {$fileSearchDocument->createTime}\n";
    echo "Update Time: {$fileSearchDocument->updateTime}\n";
    echo "--- \n";
}
```

#### Delete File Search Document
Delete a file search document by name.

```php
use Gemini\Laravel\Facades\Gemini;

Gemini::fileSearchStores()->deleteDocument('fileSearchStores/my-search-store/documents/my-document');
```

### Embeddings
Embedding is a technique used to represent information as a list of floating point numbers in an array. With Gemini, you can represent text (words, sentences, and blocks of text) in a vectorized form, making it easier to compare and contrast embeddings. For example, two texts that share a similar subject matter or sentiment should have similar embeddings, which can be identified through mathematical comparison techniques such as cosine similarity.

Use an [embedding model](https://ai.google.dev/gemini-api/docs/embeddings) such as `gemini-embedding-001` with either `embedContent` or `batchEmbedContents`:

```php
use Gemini\Laravel\Facades\Gemini;

$response = Gemini::embeddingModel('gemini-embedding-001')
    ->embedContent("Write a story about a magic backpack.");

print_r($response->embedding->values);
//[
//    [0] => 0.008624583
//    [1] => -0.030451821
//    [2] => -0.042496547
//    [3] => -0.029230341
//    [4] => 0.05486475
//    [5] => 0.006694871
//    [6] => 0.004025645
//    [7] => -0.007294857
//    [8] => 0.0057651913
//    ...
//]
```

```php
use Gemini\Laravel\Facades\Gemini;

$response = Gemini::embeddingModel('gemini-embedding-001')
    ->batchEmbedContents("This is a test", "Another text");

print_r($response->embeddings);
// [
// [0] => Gemini\Data\ContentEmbedding Object
// (
//     [values] => Array
//         (
//         [0] => 0.035855837
//         [1] => -0.049537655
//         [2] => -0.06834927
//         [3] => -0.010445258
//         [4] => 0.044641383
//         [5] => 0.031156342
//         [6] => -0.007810312
//         [7] => -0.0106866965
//         ...
//         ),
// ),
// [1] => Gemini\Data\ContentEmbedding Object
// (
//     [values] => Array
//         (
//         [0] => 0.035855837
//         [1] => -0.049537655
//         [2] => -0.06834927
//         [3] => -0.010445258
//         [4] => 0.044641383
//         [5] => 0.031156342
//         [6] => -0.007810312
//         [7] => -0.0106866965
//         ...
//         ),
// ),
// ]
```

### Models

We recommend checking [Google documentation](https://ai.google.dev/gemini-api/docs/models) for the latest supported models.

#### List Models
Use list models to see the available Gemini models programmatically:

- **pageSize (optional)**:
    The maximum number of Models to return (per page). <br>
    If unspecified, 50 models will be returned per page. This method returns at most 1000 models per page, even if you pass a larger pageSize.


- **nextPageToken (optional)**:
    A page token, received from a previous models.list call. <br>
    Provide the pageToken returned by one request as an argument to the next request to retrieve the next page.
    When paginating, all other parameters provided to models.list must match the call that provided the page token.

```php
use Gemini\Laravel\Facades\Gemini;

$response = Gemini::models()->list(pageSize: 3, nextPageToken: 'ChFtb2RlbHMvZ2VtaW5pLXBybw==');

$response->models;
//[
//    [0] => Gemini\Data\Model Object
//        (
//            [name] => models/gemini-2.5-flash
//            [version] => 001
//            [displayName] => Gemini 2.5 Flash
//            [description] => Stable version of Gemini 2.5 Flash
//            ...
//        )
//    [1] => Gemini\Data\Model Object
//        (
//            [name] => models/gemini-2.5-pro
//            [version] => 2.5
//            [displayName] => Gemini 2.5 Pro
//            [description] => Stable release of Gemini 2.5 Pro
//            ...
//        )
//    [2] => Gemini\Data\Model Object
//        (
//            [name] => models/gemini-embedding-001
//            [version] => 001
//            [displayName] => Gemini Embedding 001
//            [description] => Obtain a distributed representation of a text.
//            ...
//        )
//]
```

```php
$response->nextPageToken; // Chltb2RlbHMvZ2VtaW5pLTEuMC1wcm8tMDAx
```

#### Get Model
Get information about a model, such as version, display name, input token limit, etc.

```php
use Gemini\Laravel\Facades\Gemini;

$response = Gemini::models()->retrieve('models/gemini-2.5-flash');

$response->model;
//Gemini\Data\Model Object
//(
//    [name] => models/gemini-2.5-flash
//    [version] => 001
//    [displayName] => Gemini 2.5 Flash
//    [description] => Stable version of Gemini 2.5 Flash
//    [inputTokenLimit] => 1048576
//    [outputTokenLimit] => 65536
//    ...
//)
```

## Troubleshooting

### Error Handling

Errors returned by the API throw a `Gemini\Exceptions\ErrorException`, with the message, status and code of the API error. Other failures, such as network errors, throw a `Gemini\Exceptions\TransporterException`, and a response that is not valid JSON throws a `Gemini\Exceptions\UnserializableResponse`.

```php
use Gemini\Exceptions\ErrorException;
use Gemini\Exceptions\TransporterException;
use Gemini\Laravel\Facades\Gemini;

try {
    $response = Gemini::generativeModel(model: 'gemini-2.5-flash')->generateContent('Hello');
} catch (ErrorException $e) {
    $e->getErrorMessage(); // The model is overloaded. Please try again later.
    $e->getErrorStatus(); // UNAVAILABLE
    $e->getErrorCode(); // 503
} catch (TransporterException $e) {
    $e->getPrevious(); // the exception thrown by the HTTP client
}
```

Rate limit (`429`) and overload (`503`) errors are usually temporary and can be retried after a short wait.

### Empty Responses

`text()` and `parts()` throw a `ValueError` when the response has no content. The error message contains the reason, which is also available on the response:

```php
$response->promptFeedback?->blockReason; // the prompt was blocked, e.g. BlockReason::SAFETY
$response->candidates[0]->finishReason; // e.g. FinishReason::MAX_TOKENS or FinishReason::SAFETY
$response->candidates[0]->finishMessage; // more details, when available
```

A common cause is `MAX_TOKENS` with thinking models: the thoughts use the whole `maxOutputTokens` budget before the answer is written. Increase `maxOutputTokens` or lower the [thinking budget or level](#thinking-mode).

### Timeout

You may run into a timeout when sending requests to the API. The default timeout is 30 seconds.

You can change it with the `GEMINI_REQUEST_TIMEOUT` environment variable:

```env
GEMINI_REQUEST_TIMEOUT=60
```

Or in the `config/gemini.php` file:

```php
return [
    'api_key' => env('GEMINI_API_KEY'),
    'base_url' => env('GEMINI_BASE_URL'),
    'request_timeout' => env('GEMINI_REQUEST_TIMEOUT', 60),
];
```

## Testing

The package provides a fake implementation of the client that allows you to fake the API responses.

To test your code, call `Gemini::fake()` in your test case. It swaps the client behind the facade for a fake that returns the given responses.

The fake responses are returned in the order they are provided while creating the fake client.

All responses have a `fake()` method that creates a response object from only the parameters relevant to your test case.

```php
use Gemini\Laravel\Facades\Gemini;
use Gemini\Responses\GenerativeModel\GenerateContentResponse;

Gemini::fake([
    GenerateContentResponse::fake([
        'candidates' => [
            [
                'content' => [
                    'parts' => [
                        [
                            'text' => 'success',
                        ],
                    ],
                ],
            ],
        ],
    ]),
]);

$result = Gemini::generativeModel(model: 'gemini-2.5-flash')->generateContent('test');

expect($result->text())->toBe('success');
```

In case of a streamed response you can optionally provide a resource holding the fake response data.

```php
use Gemini\Laravel\Facades\Gemini;
use Gemini\Responses\GenerativeModel\GenerateContentResponse;

Gemini::fake([
    GenerateContentResponse::fakeStream(),
]);

$result = Gemini::generativeModel(model: 'gemini-2.5-flash')->streamGenerateContent('Hello');

expect($result->getIterator()->current())
    ->text()->toBe('In the bustling city of Aethelwood, where the cobblestone streets whispered');
```

Chats started with `startChat()` work with the fake as well. Each message is sent as a `generateContent` (or `streamGenerateContent`) request with the full history, and consumes the next fake response.

```php
use Gemini\Laravel\Facades\Gemini;
use Gemini\Responses\GenerativeModel\GenerateContentResponse;

Gemini::fake([
    GenerateContentResponse::fake(),
    GenerateContentResponse::fake(),
]);

$chat = Gemini::generativeModel(model: 'gemini-2.5-flash')->startChat();
$chat->sendMessage('Hello');
$chat->sendMessage('How are you?');

Gemini::generativeModel(model: 'gemini-2.5-flash')->assertFunctionCalled(function (string $method) {
    return $method === 'startChat';
});

Gemini::generativeModel(model: 'gemini-2.5-flash')->assertSent(function (string $method, array $parameters) {
    return $method === 'generateContent' &&
        count($parameters) === 3; // Hello, the model's answer, How are you?
});
```

After the requests have been sent, use the assertion methods to check that the expected requests were sent:

```php
// assert list models request was sent
use Gemini\Laravel\Facades\Gemini;
use Gemini\Resources\GenerativeModel;
use Gemini\Resources\Models;

Gemini::models()->assertSent(callback: function ($method) {
    return $method === 'list';
});
// or
Gemini::assertSent(resource: Models::class, callback: function ($method) {
    return $method === 'list';
});

Gemini::generativeModel(model: 'gemini-2.5-flash')->assertSent(function (string $method, array $parameters) {
    return $method === 'generateContent' &&
        $parameters[0] === 'Hello';
});
// or
Gemini::assertSent(resource: GenerativeModel::class, model: 'gemini-2.5-flash', callback: function (string $method, array $parameters) {
    return $method === 'generateContent' &&
        $parameters[0] === 'Hello';
});

// assert 2 generative model requests were sent
Gemini::assertSent(resource: GenerativeModel::class, model: 'gemini-2.5-flash', callback: 2);
// or
Gemini::generativeModel(model: 'gemini-2.5-flash')->assertSent(2);

// assert no generative model requests were sent
Gemini::assertNotSent(resource: GenerativeModel::class, model: 'gemini-2.5-flash');
// or
Gemini::generativeModel(model: 'gemini-2.5-flash')->assertNotSent();

// assert no requests were sent
Gemini::assertNothingSent();
```

Calls that configure a model, like `withGenerationConfig()`, `withTool()` or `startChat()`, are recorded as function calls:

```php
use Gemini\Data\GenerationConfig;
use Gemini\Laravel\Facades\Gemini;

Gemini::generativeModel(model: 'gemini-2.5-flash')->assertFunctionCalled(function (string $method, array $parameters) {
    return $method === 'withGenerationConfig' &&
        $parameters[0] instanceof GenerationConfig;
});
```

To test a failing request, provide a `Throwable` as the response.

```php
use Gemini\Exceptions\ErrorException;
use Gemini\Laravel\Facades\Gemini;

Gemini::fake([
    new ErrorException([
        'message' => 'The model `gemini-basic` does not exist',
        'status' => 'INVALID_ARGUMENT',
        'code' => 400,
    ]),
]);

// the `ErrorException` will be thrown
Gemini::generativeModel(model: 'gemini-2.5-flash')->generateContent('test');
```
