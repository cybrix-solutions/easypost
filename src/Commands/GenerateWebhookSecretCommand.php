<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'easypost:generate-webhook-secret')]
final class GenerateWebhookSecretCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'easypost:generate-webhook-secret
                            {--show : Display the secret instead of modifying files}
                            {--force : Force the operation to run even when a secret is already set}';

    protected $description = 'Generate a new webhook secret for EasyPost.';

    public function handle(): void
    {
        $secret = $this->generateRandomSecret();

        if ($this->option('show')) {
            $this->line("<comment>{$secret}</comment>");

            return;
        }

        if (! $this->setSecretInEnvironmentFile($secret)) {
            return;
        }

        $this->laravel['config']['easypost.webhook_secret'] = $secret;

        $this->components->info('EasyPost webhook secret set successfully.');
    }

    private function generateRandomSecret(): string
    {
        return bin2hex(random_bytes(32));
    }

    private function setSecretInEnvironmentFile(string $secret): bool
    {
        $currentSecret = $this->laravel['config']['easypost.webhook_secret'];

        $confirmed = $this->confirmToProceed('EasyPost webhook secret already set', function () use ($currentSecret) {
            return filled($currentSecret);
        });

        if (! $confirmed) {
            return false;
        }

        if (! $this->writeNewEnvironmentFileWith($secret)) {
            return false;
        }

        return true;
    }

    private function writeNewEnvironmentFileWith(string $secret): bool
    {
        $replaced = preg_replace(
            $this->secretReplacementPattern(),
            'EASYPOST_WEBHOOK_SECRET=' . $secret,
            $input = file_get_contents($this->laravel->environmentFilePath())
        );

        if ($replaced === $input || $replaced === null) {
            $replaced .= PHP_EOL . 'EASYPOST_WEBHOOK_SECRET=' . $secret;
        }

        file_put_contents($this->laravel->environmentFilePath(), $replaced);

        return true;
    }

    /**
     * Get the regex pattern that will match env EASYPOST_WEBHOOK_SECRET with any random secret.
     */
    private function secretReplacementPattern(): string
    {
        $escaped = preg_quote('=' . $this->laravel['config']['easypost.webhook_secret'], '/');

        return "/^EASYPOST_WEBHOOK_SECRET{$escaped}/m";
    }
}
