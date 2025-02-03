<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Actions\ParcelTracking;

use CybrixSolutions\EasyPost\Contracts\Models\Parcel;
use CybrixSolutions\EasyPost\Contracts\ParcelTracking\UpdateTrackingAction as UpdateTrackingActionContract;
use CybrixSolutions\EasyPost\Enums\ShipmentStatusEnum;
use CybrixSolutions\EasyPost\Events\ParcelTracking\ParcelTrackingWasUpdated;
use CybrixSolutions\EasyPost\Services\TrackerService;

class UpdateTrackingAction implements UpdateTrackingActionContract
{
    public function __construct(protected TrackerService $api) {}

    public function __invoke(Parcel $parcel): void
    {
        if (! $parcel->tracker_id) {
            return;
        }

        $tracker = $this->api->retrieve($parcel->tracker_id);

        $parcel->fill([
            'last_tracked_at' => now(),
            'status' => $tracker->status,
        ]);

        foreach ($tracker->tracking_details as $detail) {
            $trackingModel = $parcel->tracking()->firstOrNew([
                'status_code' => $detail->status,
                'activity_date' => $detail->datetime,
            ]);

            $trackingModel->fill([
                'status' => $detail->message,
                'state' => $detail->tracking_location?->state,
                'city' => $detail->tracking_location?->city,
            ]);

            $trackingModel->save();

            $shipmentStatus = ShipmentStatusEnum::tryFrom($detail->status);
            if ($shipmentStatus === ShipmentStatusEnum::InTransit && ! $parcel->picked_up_at) {
                $parcel->picked_up_at = $detail->datetime;
            }

            if ($shipmentStatus === ShipmentStatusEnum::Delivered) {
                $parcel->signed_by = $tracker->signed_by;
                $parcel->delivered_at = $detail->datetime;
            }
        }

        $statusChanged = $parcel->isDirty('status');
        $parcel->saveQuietly();

        event(new ParcelTrackingWasUpdated($parcel, ShipmentStatusEnum::tryFrom($tracker->status), $statusChanged));
    }
}
