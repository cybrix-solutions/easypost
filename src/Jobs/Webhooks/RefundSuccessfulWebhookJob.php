<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Jobs\Webhooks;

use CybrixSolutions\EasyPost\Contracts\Models\Shipment;
use CybrixSolutions\EasyPost\Enums\ShipmentRefundStatusEnum;
use CybrixSolutions\EasyPost\Events\Shipments\ShipmentWasRefunded;
use CybrixSolutions\EasyPost\Models\WebhookCall;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

/**
 * This webhook is responsible for handling non-instantaneous refunds,
 * typically from USPS.
 *
 * @see https://www.easypost.com/docs/api/curl#events
 */
class RefundSuccessfulWebhookJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public WebhookCall $webhookCall) {}

    public function handle(): void
    {
        $result = Arr::get($this->webhookCall->payload, 'result');
        if (! $result) {
            return;
        }

        $shipment = app(Shipment::class)::findByEasyPostId(
            Arr::get($result, 'shipment_id'),
        );

        if (! $shipment) {
            return;
        }

        if (! $shipment->voided_at) {
            $shipment->voided_at = now();
        }

        // Status should always be "refunded" for this webhook.
        $shipment->refund_status = Arr::get($result, 'status');
        $shipment->saveQuietly();

        if ($shipment->refund_status !== ShipmentRefundStatusEnum::Rejected) {
            ShipmentWasRefunded::dispatch($shipment, $result);
        }
    }
}
