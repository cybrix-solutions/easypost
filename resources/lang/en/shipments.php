<?php

declare(strict_types=1);

return [
    'alerts' => [
        'already_picked_up_cannot_void' => 'Shipments cannot be voided if they are already picked up.',
        'already_voided' => 'Shipment is already voided.',
        'shipment_voided' => 'Shipment was voided!',
        'unable_to_void' => 'Shipment is not able to be voided at this time.',
        'unable_to_void_outside_of_void_period' => 'Shipment is outside the allowed void window for this carrier.',
    ],

    'labels' => [
        'dim_weight' => 'Dimensional weight',
        'dim_weight_help' => "Dimensional weight reflects package density, which is the amount of space a package occupies in relation to its actual weight. It is calculated by dividing the package's volume by a divisor set by the carrier and rounded up to the nearest pound. The current calculation is: (:length x :width x :height) ÷ :divisor",
        'weight_display' => ':weight lb|:weight lbs',
    ],

    'tracking' => [
        'alerts' => [
            'never_tracked' => 'Never',
            'no_tracking_available' => 'No tracking information available for this shipment.',
            'no_tracking_found' => 'No tracking information found for package. Please check back later.',
            'not_picked_up' => 'Not picked up',
            'general_error' => 'No tracking information available.',
        ],
    ],
];
