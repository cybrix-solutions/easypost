<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\View\Components;

use CybrixSolutions\EasyPost\Dto\EasyPostWebhook;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class WebhookResult extends Component
{
    public function __construct(public EasyPostWebhook $webhook)
    {
    }

    public function webhookType(): string
    {
        return $this->webhook->mode === 'production'
            ? __('easypost::webhooks.labels.production_webhook')
            : __('easypost::webhooks.labels.test_webhook');
    }

    public function render(): View
    {
        return view('easypost::components.webhook-result');
    }
}
