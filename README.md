<p align="center">
    <img src="https://raw.githubusercontent.com/google-gemini-php/laravel/main/art/example.png" width="600" alt="Google Gemini PHP for Laravel">
    <p align="center">
        <a href="https://packagist.org/packages/google-gemini-php/laravel"><img alt="Latest Version" src="https://img.shields.io/packagist/v/google-gemini-php/laravel"></a>
        <a href="https://packagist.org/packages/google-gemini-php/laravel"><img alt="License" src="https://img.shields.io/github/license/google-gemini-php/laravel"></a>
    </p>
</p>

------

**Gemini PHP** for Laravel is a community-maintained PHP API client that allows you to interact with the Gemini AI API.

- Fatih AYDIN [github.com/aydinfatih](https://github.com/aydinfatih)
- Vytautas Smilingis [github.com/Plytas](https://github.com/Plytas)

For more information, take a look at the [google-gemini-php/client](https://github.com/google-gemini-php/client) repository.

## Table of Contents
- [Prerequisites](#prerequisites)
- [Setup](#setup)
    - [Installation](#installation)
    - [Setup your API key](#setup-your-api-key)
    - [Upgrade to 2.0](#upgrade-to-20)
- [Usage](#usage)
    - [Chat Resource](#chat-resource)
        - [Text-only Input](#text-only-input)
        - [Text-and-image Input](#text-and-image-input)
        - [File Upload](#file-upload)
        - [Text-and-video Input](#text-and-video-input)
        - [Multi-turn Conversations (Chat)](#multi-turn-conversations-chat)
        - [Stream Generate Content](#stream-generate-content)
        - [Structured Output](#structured-output)
        - [Function calling](#function-calling)
        - [Count tokens](#count-tokens)
        - [Configuration](#configuration)
    - [Embedding Resource](#embedding-resource)
    - [Models](#models)
        - [List Models](#list-models)
        - [Get Model](#get-model)
- [Testing](#testing)


## Prerequisites
To complete this quickstart, make sure that your development environment meets the following requirements:

- Requires [PHP 8.1+](https://php.net/releases/)
- Requires [Laravel 9,10,11,12](https://laravel.com/)

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

This will create a config/gemini.php configuration file in your project, which you can modify to your needs using environment variables. Blank environment variables for the Gemini API key is already appended to your .env file.

```
GEMINI_API_KEY=
```

You can also define the following environment variables.
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

`\Gemini\Enums\ModelType` enum has been deprecated and will be removed in next major version. Together with this `Gemini::geminiPro()` and `Gemini::geminiFlash()` methods have been removed.
We suggest using `Gemini::generativeModel()` method and pass in the model string directly. All methods that had previously accepted `ModelType` enum now accept a `BackedEnum`. We recommend implementing your own enum for convenience.

There may be other breaking changes not listed here. If you encounter any issues, please submit an issue or a pull request.

## Usage

Interact with Gemini's API:

```php
use Gemini\Laravel\Facades\Gemini;

$result = Gemini::generativeModel(model: 'gemini-2.0-flash')->generateContent('Hello');

$result->text(); // Hello! How can I assist you today?
```

### Chat Resource

For a complete list of supported input formats and methods in Gemini API v1, see the [models documentation](https://ai.google.dev/gemini-api/docs/models).

#### Text-only Input
Generate a response from the model given an input message.

```php
use Gemini\Laravel\Facades\Gemini;

$result = Gemini::generativeModel(model: 'gemini-2.0-flash')->generateContent('Hello');

$result->text(); // Hello! How can I assist you today?
```

#### Text-and-image Input
Generate responses by providing both text prompts and images to the Gemini model.

```php
use Gemini\Data\Blob;
use Gemini\Enums\MimeType;
use Gemini\Laravel\Facades\Gemini;

$result = Gemini::generativeModel(model: 'gemini-2.0-flash')
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

#### Text-and-video Input
Process video content and get AI-generated descriptions using the Gemini API with an uploaded video file.

```php
use Gemini\Data\UploadedFile;
use Gemini\Enums\MimeType;
use Gemini\Laravel\Facades\Gemini;

$result = Gemini::generativeModel(model: 'gemini-2.0-flash')
    ->generateContent([
        'What is this video?',
        new UploadedFile(
            fileUri: '123-456', // accepts just the name or the full URI
            mimeType: MimeType::VIDEO_MP4
        )
    ]);

$result->text(); //  The picture shows a table with a white tablecloth. On the table are two cups of coffee, a bowl of blueberries, a silver spoon, and some flowers. There are also some blueberry scones on the table.
```

#### Multi-turn Conversations (Chat)
Using Gemini, you can build freeform conversations across multiple turns.

```php
use Gemini\Data\Content;
use Gemini\Enums\Role;
use Gemini\Laravel\Facades\Gemini;

$chat = Gemini::chat(model: 'gemini-2.0-flash')
    ->startChat(history: [
        Content::parse(part: 'The stories you write about what I have to say should be one line. Is that clear?'),
        Content::parse(part: 'Yes, I understand. The stories I write about your input should be one line long.', role: Role::MODEL)
    ]);

$response = $chat->sendMessage('Create a story set in a quiet village in 1600s France');
echo $response->text(); // Amidst rolling hills and winding cobblestone streets, the tranquil village of Beausoleil whispered tales of love, intrigue, and the magic of everyday life in 17th century France.

$response = $chat->sendMessage('Rewrite the same story in 1600s England');
echo $response->text(); // In the heart of England's lush countryside, amidst emerald fields and thatched-roof cottages, the village of Willowbrook unfolded a tapestry of love, mystery, and the enchantment of ordinary days in the 17th century.

```

#### Stream Generate Content
By default, the model returns a response after completing the entire generation process. You can achieve faster interactions by not waiting for the entire result, and instead use streaming to handle partial results.

```php
$stream = Gemini::generativeModel(model: 'gemini-2.0-flash')
    ->streamGenerateContent('Write long a story about a magic backpack.');

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

$result = Gemini::generativeModel(model: 'gemini-2.0-flash')
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

#### Function calling
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
                    )
                )
            ],
            role: Role::USER
        );
    }

    //Handle other function calls
}

$chat = Gemini::generativeModel(model: 'gemini-2.0-flash')
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

#### Count tokens
When using long prompts, it might be useful to count tokens before sending any content to the model.

```php
use Gemini\Laravel\Facades\Gemini;

$response = Gemini::generativeModel(model: 'gemini-2.0-flash')
    ->countTokens('Write a story about a magic backpack.');

echo $response->totalTokens; // 9
```

#### Configuration
Every prompt you send to the model includes parameter values that control how the model generates a response. The model can generate different results for different parameter values. Learn more about [model parameters](https://ai.google.dev/docs/concepts#model_parameters).

Also, you can use safety settings to adjust the likelihood of getting responses that may be considered harmful. By default, safety settings block content with medium and/or high probability of being unsafe content across all dimensions. Learn more about [safety settings](https://ai.google.dev/docs/concepts#safety_setting).


```php
use Gemini\Data\GenerationConfig;
use Gemini\Enums\HarmBlockThreshold;
use Gemini\Data\SafetySetting;
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

$generativeModel = Gemini::generativeModel(model: 'gemini-2.0-flash')
    ->withSafetySetting($safetySettingDangerousContent)
    ->withSafetySetting($safetySettingHateSpeech)
    ->withGenerationConfig($generationConfig)
    ->generateContent("Write a story about a magic backpack.");
```

### Embedding Resource
Embedding is a technique used to represent information as a list of floating point numbers in an array. With Gemini, you can represent text (words, sentences, and blocks of text) in a vectorized form, making it easier to compare and contrast embeddings. For example, two texts that share a similar subject matter or sentiment should have similar embeddings, which can be identified through mathematical comparison techniques such as cosine similarity.

Use the `text-embedding-004` model with either `embedContents` or `batchEmbedContents`:

```php
use Gemini\Laravel\Facades\Gemini;

$response = Gemini::embeddingModel('text-embedding-004')
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

### Models

We recommend checking [Google documentation](https://ai.google.dev/gemini-api/docs/models) for the latest supported models.

#### List Models
Use list models to see the available Gemini models:

```php
use Gemini\Laravel\Facades\Gemini;

$response = Gemini::models()->list();

$response->models;
//[
//    [0] => Gemini\Data\Model Object
//        (
//            [name] => models/gemini-2.0-flash
//            [version] => 2.0
//            [displayName] => Gemini 2.0 Flash
//            [description] => Gemini 2.0 Flash
//            ...
//        )
//    [1] => Gemini\Data\Model Object
//        (
//            [name] => models/gemini-2.5-pro-preview-05-06
//            [version] => 2.5-preview-05-06
//            [displayName] => Gemini 2.5 Pro Preview 05-06
//            [description] => Preview release (May 6th, 2025) of Gemini 2.5 Pro
//            ...
//        )
//    [2] => Gemini\Data\Model Object
//        (
//            [name] => models/text-embedding-004
//            [version] => 004
//            [displayName] => Text Embedding 004
//            [description] => Obtain a distributed representation of a text.
//            ...
//        )
//]
```

#### Get Model
Get information about a model, such as version, display name, input token limit, etc.
```php
use Gemini\Laravel\Facades\Gemini;

$response = Gemini::models()->retrieve('models/gemini-2.5-pro-preview-05-06');

$response->model;
//Gemini\Data\Model Object
//(
//    [name] => models/gemini-2.5-pro-preview-05-06
//    [version] => 2.5-preview-05-06
//    [displayName] => Gemini 2.5 Pro Preview 05-06
//    [description] => Preview release (May 6th, 2025) of Gemini 2.5 Pro
//    ...
//)
```

## Testing

The package provides a fake implementation of the `Gemini\Client` class that allows you to fake the API responses.

To test your code ensure you swap the `Gemini\Client` class with the `Gemini\Testing\ClientFake` class in your test case.

The fake responses are returned in the order they are provided while creating the fake client.

All responses are having a `fake()` method that allows you to easily create a response object by only providing the parameters relevant for your test case.

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

$result = Gemini::generativeModel(model: 'gemini-2.0-flash')->generateContent('test');

expect($result->text())->toBe('success');
```

In case of a streamed response you can optionally provide a resource holding the fake response data.

```php
use Gemini\Laravel\Facades\Gemini;
use Gemini\Responses\GenerativeModel\GenerateContentResponse;

Gemini::fake([
    GenerateContentResponse::fakeStream(),
]);

$result = Gemini::generativeModel(model: 'gemini-2.0-flash')->streamGenerateContent('Hello');

expect($response->getIterator()->current())
    ->text()->toBe('In the bustling city of Aethelwood, where the cobblestone streets whispered');
```

After the requests have been sent there are various methods to ensure that the expected requests were sent:

```php
use Gemini\Laravel\Facades\Gemini;
use Gemini\Resources\GenerativeModel;
use Gemini\Resources\Models;

// assert list models request was sent
Gemini::models()->assertSent(callback: function ($method) {
    return $method === 'list';
});
// or
Gemini::assertSent(resource: Models::class, callback: function ($method) {
    return $method === 'list';
});

Gemini::generativeModel(model: 'gemini-2.0-flash')->assertSent(function (string $method, array $parameters) {
    return $method === 'generateContent' &&
        $parameters[0] === 'Hello';
});
// or
Gemini::assertSent(resource: GenerativeModel::class, model: 'gemini-2.0-flash', callback: function (string $method, array $parameters) {
    return $method === 'generateContent' &&
        $parameters[0] === 'Hello';
});


// assert 2 generative model requests were sent
Gemini::assertSent(resource: GenerativeModel::class, model: 'gemini-2.0-flash', callback: 2);
// or
Gemini::geminiPro()->assertSent(2);

// assert no generative model requests were sent
Gemini::assertNotSent(resource: GenerativeModel::class, model: 'gemini-2.0-flash');
// or
Gemini::geminiPro()->assertNotSent();

// assert no requests were sent
Gemini::assertNothingSent();
```

To write tests expecting the API request to fail you can provide a `Throwable` object as the response.

```php
use Gemini\Laravel\Facades\Gemini;
use Gemini\Exceptions\ErrorException;

Gemini::fake([
    new ErrorException([
        'message' => 'The model `gemini-basic` does not exist',
        'status' => 'INVALID_ARGUMENT',
        'code' => 400,
    ]),
]);

// the `ErrorException` will be thrown
Gemini::generativeModel(model: 'gemini-2.0-flash')->generateContent('test');
```
