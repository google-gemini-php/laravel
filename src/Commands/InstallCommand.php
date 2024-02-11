<?php

declare(strict_types=1);

namespace Gemini\Laravel\Commands;

use Gemini\Laravel\ServiceProvider;
use Gemini\Laravel\Support\View;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    private const LINKS = [
        'Repository' => 'https://github.com/google-gemini-php/laravel',
        'Gemini PHP Docs' => 'https://github.com/google-gemini-php/client#readme',
    ];

    protected $signature = 'gemini:install';

    protected $description = 'Prepares the Gemini client for use.';

    public function handle(): void
    {
        View::renderUsing($this->output);

        View::render('components.badge', [
            'type' => 'INFO',
            'content' => 'Installing Gemini for Laravel.',
        ]);

        $this->copyConfig();

        View::render('components.new-line');

        $this->addEnvKeys('.env');
        $this->addEnvKeys('.env.example');

        View::render('components.new-line');

        $wantsToSupport = $this->askToStarRepository();

        $this->showLinks();

        View::render('components.badge', [
            'type' => 'INFO',
            'content' => 'Open your .env and add your Gemini API key.',
        ]);

        if ($wantsToSupport) {
            $this->openRepositoryInBrowser();
        }
    }

    private function copyConfig(): void
    {
        if (file_exists(config_path('gemini.php'))) {
            View::render('components.two-column-detail', [
                'left' => 'config/gemini.php',
                'right' => 'File already exists.',
            ]);

            return;
        }

        View::render('components.two-column-detail', [
            'left' => 'config/gemini.php',
            'right' => 'File created.',
        ]);

        $this->callSilent('vendor:publish', [
            '--provider' => ServiceProvider::class,
        ]);
    }

    private function addEnvKeys(string $envFile): void
    {
        $fileContent = file_get_contents(base_path($envFile));

        if ($fileContent === false) {
            return;
        }

        if (str_contains($fileContent, 'GEMINI_API_KEY')) {
            View::render('components.two-column-detail', [
                'left' => $envFile,
                'right' => 'Variables already exists.',
            ]);

            return;
        }

        file_put_contents(base_path($envFile), PHP_EOL.'GEMINI_API_KEY='.PHP_EOL, FILE_APPEND);

        View::render('components.two-column-detail', [
            'left' => $envFile,
            'right' => 'GEMINI_API_KEY variable added.',
        ]);
    }

    private function askToStarRepository(): bool
    {
        if (! $this->input->isInteractive()) {
            return false;
        }

        return $this->confirm(' <options=bold>Wanna show Gemini for Laravel some love by starring it on GitHub?</>', false);
    }

    private function openRepositoryInBrowser(): void
    {
        if (PHP_OS_FAMILY == 'Darwin') {
            exec('open https://github.com/google-gemini-php/laravel');
        }
        if (PHP_OS_FAMILY == 'Windows') {
            exec('start https://github.com/google-gemini-php/laravel');
        }
        if (PHP_OS_FAMILY == 'Linux') {
            exec('xdg-open https://github.com/google-gemini-php/laravel');
        }
    }

    private function showLinks(): void
    {
        $links = [
            ...self::LINKS,
        ];

        foreach ($links as $message => $link) {
            View::render('components.two-column-detail', [
                'left' => $message,
                'right' => $link,
            ]);
        }
    }
}
