<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Jobs\Webhooks;

use CybrixSolutions\EasyPost\Contracts\Models\Shipment;
use CybrixSolutions\EasyPost\Models\WebhookCall;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class TrackerCreatedJob implements ShouldQueue
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

        /** @var \CybrixSolutions\EasyPost\Models\Shipment $shipment */
        $shipment = app(Shipment::class)::query()
            ->where('easypost_id', Arr::get($result, 'shipment_id'))
            ->with('firstParcel') // We only need the first parcel since EasyPost is single parcel shipments.
            ->first();

        if (! $shipment || ! $shipment->firstParcel) {
            return;
        }

        // Sometimes the tracker id given when creating a shipment is not the same one
        // we receive in the webhook,  we'll update it now if so. This is mostly
        // relevant in test mode.
        $trackerId = Arr::get($result, 'id');
        if ($trackerId && $trackerId !== $shipment->firstParcel->tracker_id) {
            $shipment->firstParcel->tracker_id = $trackerId;
            $shipment->firstParcel->saveQuietly();
        }
    }
}
