<?php

use Gemini\Enums\ModelType;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Laravel\ServiceProvider;
use Gemini\Resources\GenerativeModel;
use Gemini\Responses\GenerativeModel\GenerateContentResponse;
use Illuminate\Config\Repository;
use PHPUnit\Framework\ExpectationFailedException;

it('resolves resources', function () {
    $app = app();

    $app->bind('config', fn () => new Repository([
        'gemini' => [
            'api_key' => 'test',
        ],
    ]));

    (new ServiceProvider($app))->register();

    Gemini::setFacadeApplication($app);

    $generativeModel = Gemini::geminiPro();

    expect($generativeModel)->toBeInstanceOf(GenerativeModel::class);
});

test('fake returns the given response', function () {
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

    $result = Gemini::geminiPro()->generateContent('Php is');

    expect($result->text())->toBe('success');
});

test('fake throws an exception if there is no more given response', function () {
    Gemini::fake([
        GenerateContentResponse::fake(),
    ]);
    Gemini::geminiPro()->generateContent('Php is');

    Gemini::geminiPro()->generateContent('Php is');
})->expectExceptionMessage('No fake responses left');

test('append more fake responses', function () {
    Gemini::fake([
        GenerateContentResponse::fake([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            [
                                'text' => 'response-1',
                            ],
                        ],
                    ],
                ],
            ],
        ]),
    ]);

    Gemini::addResponses([
        GenerateContentResponse::fake([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            [
                                'text' => 'response-2',
                            ],
                        ],
                    ],
                ],
            ],
        ]),
    ]);

    $result = Gemini::geminiPro()->generateContent('Php is');

    expect($result->text())->toBe('response-1');

    $result = Gemini::geminiPro()->generateContent('Php is');

    expect($result->text())->toBe('response-2');
});

test('fake can assert a request was sent', function () {
    Gemini::fake([
        GenerateContentResponse::fake(),
    ]);

    Gemini::geminiPro()->generateContent('test');

    Gemini::assertSent(resource: GenerativeModel::class, model: ModelType::GEMINI_PRO, callback: function (string $method, array $parameters) {
        return $method === 'generateContent' &&
            $parameters[0] === 'test';
    });
});

test('fake throws an exception if a request was not sent', function () {
    Gemini::fake([
        GenerateContentResponse::fake(),
    ]);

    Gemini::assertSent(resource: GenerativeModel::class, callback: function (string $method, array $parameters) {
        return $method === 'create' &&
            $parameters['model'] === 'gpt-3.5-turbo-instruct' &&
            $parameters['prompt'] === 'PHP is ';
    });
})->expectException(ExpectationFailedException::class);

test('fake can assert a request was sent on the resource', function () {
    Gemini::fake([
        GenerateContentResponse::fake(),
    ]);

    Gemini::geminiPro()->generateContent('test');

    Gemini::geminiPro()->assertSent(function (string $method, array $parameters): bool {
        return $method === 'generateContent' &&
            $parameters[0] === 'test';
    });
});

test('fake can assert a request was sent n times', function () {
    Gemini::fake([
        GenerateContentResponse::fake(),
        GenerateContentResponse::fake(),
    ]);

    Gemini::geminiPro()->generateContent('test');

    Gemini::geminiPro()->generateContent('test');

    Gemini::assertSent(GenerativeModel::class, ModelType::GEMINI_PRO, 2);
});

test('fake throws an exception if a request was not sent n times', function () {
    Gemini::fake([
        GenerateContentResponse::fake(),
        GenerateContentResponse::fake(),
    ]);

    Gemini::geminiPro()->generateContent('test');

    Gemini::assertSent(GenerativeModel::class, ModelType::GEMINI_PRO, 2);
})->expectException(ExpectationFailedException::class);

test('fake can assert a request was not sent', function () {
    Gemini::fake();

    Gemini::assertNotSent(GenerativeModel::class);
});

test('fake throws an exception if a unexpected request was sent', function () {
    Gemini::fake([
        GenerateContentResponse::fake(),
    ]);

    Gemini::geminiPro()->generateContent('test');

    Gemini::assertNotSent(GenerativeModel::class);
})->expectException(ExpectationFailedException::class);

test('fake can assert a request was not sent on the resource', function () {
    Gemini::fake([
        GenerateContentResponse::fake(),
    ]);

    Gemini::geminiPro()->assertNotSent();
});

test('fake can assert no request was sent', function () {
    Gemini::fake();

    Gemini::assertNothingSent();
});

test('fake throws an exception if any request was sent when non was expected', function () {
    Gemini::fake([
        GenerateContentResponse::fake(),
    ]);

    Gemini::geminiPro()->generateContent('test');

    Gemini::assertNothingSent();
})->expectException(ExpectationFailedException::class);
