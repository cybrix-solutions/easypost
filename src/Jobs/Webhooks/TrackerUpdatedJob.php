<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Jobs\Webhooks;

use CybrixSolutions\EasyPost\Contracts\Models\Parcel;
use CybrixSolutions\EasyPost\Contracts\Models\ParcelTracking;
use CybrixSolutions\EasyPost\Enums\ShipmentStatusEnum;
use CybrixSolutions\EasyPost\Events\ParcelTracking\ParcelTrackingWasUpdated;
use CybrixSolutions\EasyPost\Models\WebhookCall;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class TrackerUpdatedJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public WebhookCall $webhookCall)
    {
    }

    public function handle(): void
    {
        $result = Arr::get($this->webhookCall->payload, 'result');
        if (! $result) {
            return;
        }

        /** @var \CybrixSolutions\EasyPost\Models\Parcel $parcel */
        $parcel = app(Parcel::class)::query()
            ->where('tracker_id', Arr::get($result, 'id'))
            ->first();

        if (! $parcel) {
            return;
        }

        $status = $result['status'] ?? ShipmentStatusEnum::Unknown->value;

        $parcel->fill([
            'last_tracked_at' => now(),
            'status' => $status,
        ]);

        $trackingDetails = Arr::get($result, 'tracking_details', []);

        if (Arr::get($this->webhookCall->payload, 'mode') === 'test') {
            // The dates change in test mode for some reason, so we'll delete all tracking to avoid duplicates.
            $parcel->tracking()->cursor()->each(fn (ParcelTracking $item) => $item->deleteQuietly());
        }

        foreach ($trackingDetails as $detail) {
            /** @var \CybrixSolutions\EasyPost\Models\ParcelTracking $tracker */
            $tracker = $parcel->tracking()->firstOrNew([
                'status_code' => $detail['status'] ?? ShipmentStatusEnum::Unknown->value,
                'activity_date' => $detail['datetime'] ?? now(),
            ]);

            $tracker->fill([
                'status' => $detail['message'] ?? 'Unknown',
                'state' => Arr::get($detail, 'tracking_location.state'),
                'city' => Arr::get($detail, 'tracking_location.city'),
            ]);
            $tracker->saveQuietly();

            $enum = ShipmentStatusEnum::tryFrom($detail['status'] ?? '');
            if ($enum === ShipmentStatusEnum::InTransit && ! $parcel->picked_up_at) {
                $parcel->picked_up_at = $detail['datetime'] ?? now();
            }

            if ($enum === ShipmentStatusEnum::Delivered) {
                $parcel->signed_by = $result['signed_by'] ?? null;
                $parcel->delivered_at = $detail['datetime'] ?? now();
            }
        }

        $statusChanged = $parcel->isDirty('status');
        $parcel->saveQuietly();

        ParcelTrackingWasUpdated::dispatch(
            $parcel,
            ShipmentStatusEnum::tryFrom($status),
            $statusChanged,
        );
    }
}
