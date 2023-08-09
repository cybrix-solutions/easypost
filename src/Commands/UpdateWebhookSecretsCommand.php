<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Commands;

use CybrixSolutions\EasyPost\Contracts\Webhooks\UpdateWebhookAction;
use CybrixSolutions\EasyPost\Dto\EasyPostWebhook;
use CybrixSolutions\EasyPost\Exceptions\Webhooks\WebhookUpdateFailed;
use CybrixSolutions\EasyPost\Services\WebhooksService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'easypost:update-webhook-secrets')]
final class UpdateWebhookSecretsCommand extends Command
{
    protected $signature = 'easypost:update-webhook-secrets';

    protected $description = 'Update the webhook secrets for all registered EasyPost webhooks.';

    public function handle(WebhooksService $api, UpdateWebhookAction $updater): void
    {
        $api->all()->each(function (EasyPostWebhook $webhook) use ($updater) {
            $this->info("Updating webhook ID: {$webhook->id}...");

            try {
                $updater(
                    webhookId: $webhook->id,
                    webhookSecret: config('easypost.webhook_secret'),
                    testMode: $webhook->mode === 'test',
                );
            } catch (WebhookUpdateFailed $e) {
                $this->error("Update failed for {$webhook->id}: {$e->getMessage()}");
            }
        });

        $this->info('All webhooks were updated');
    }
}
