<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Services\Webhooks;

use CybrixSolutions\EasyPost\Events\Webhooks\InvalidWebhookSignatureEvent;
use CybrixSolutions\EasyPost\Exceptions\Webhooks\InvalidWebhookSignature;
use CybrixSolutions\EasyPost\Jobs\Webhooks\ProcessWebhookJob;
use CybrixSolutions\EasyPost\Models\WebhookCall;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebhookProcessor
{
    public function __construct(protected Request $request, protected WebhookConfig $config) {}

    public function process(): Response
    {
        $this->ensureValidSignature();

        if (! $this->config->webhookProfile->shouldProcess($this->request)) {
            return $this->createResponse();
        }

        $webhookCall = $this->storeWebhook();

        $this->processWebhook($webhookCall);

        return $this->createResponse();
    }

    protected function ensureValidSignature(): self
    {
        $validator = new HmacSignatureValidator;
        if (! $validator->isValid($this->request, $this->config)) {
            InvalidWebhookSignatureEvent::dispatch($this->request);

            throw InvalidWebhookSignature::make();
        }

        return $this;
    }

    protected function storeWebhook(): WebhookCall
    {
        return $this->config->webhookCallModel::storeWebhook($this->config, $this->request);
    }

    protected function processWebhook(WebhookCall $webhookCall): void
    {
        try {
            $job = new ProcessWebhookJob($webhookCall);

            $webhookCall->clearException();

            dispatch($job);
        } catch (Exception $e) {
            $webhookCall->saveException($e);

            throw $e;
        }
    }

    protected function createResponse(): Response
    {
        return $this->config->webhookResponse->respondToValidWebhook($this->request, $this->config);
    }
}
