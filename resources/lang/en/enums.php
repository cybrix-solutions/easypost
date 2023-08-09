<?php

declare(strict_types=1);

return [
    'delivery_confirmation' => [
        'adult' => 'Adult Signature',
        'adult_description' => 'Requires the signature of someone at the delivery address, who is 18 years of age or older.',
        'adult_signature_restricted' => 'Adult Signature Restricted',
        'adult_signature_restricted_description' => 'Requires the signature of the addressee only, who must be 21 years of age or older.',
        'gso_standard_signature' => 'Standard Signature',
        'gso_standard_signature_description' => 'A signature is required to receive the package.',
        'indirect_signature' => 'Indirect Signature',
        'indirect_signature_description' => 'Requires the signature of someone at the delivery address or from somebody nearby, such as a neighbor.',
        'none' => 'No Signature',
        'none_description' => 'Leave the package at the door. This is the equivalent to releasing liability.',
        'signature' => 'Signature',
        'signature_description' => 'Requires the signature of someone at the address.',
        'signature_restricted' => 'Signature Restricted',
        'signature_restricted_description' => 'Requires the signature of the recipient or a responsible party.',
    ],

    'shipment_direction' => [
        'forward' => 'Forward',
        'forward_description' => 'Shipment is being sent to an assignee.',
        'return' => 'Return',
        'return_description' => 'Shipment is being sent (returned) to your organization.',
    ],

    'shipment_refund_status' => [
        'refunded' => 'Refunded',
        'rejected' => 'Rejected',
        'submitted' => 'Submitted',
    ],

    'shipment_status' => [
        'available_for_pickup' => 'Available for Pickup',
        'available_for_pickup_description' => "The shipment is available for pickup from the carrier's facility.",
        'cancelled' => 'Cancelled',
        'cancelled_description' => 'The delivery has been cancelled.',
        'delivered' => 'Delivered',
        'delivered_description' => 'The shipment has been delivered.',
        'error' => 'Error',
        'error_description' => 'Unknown delivery error.',
        'failure' => 'Failure',
        'failure_description' => 'The delivery has failed.',
        'in_transit' => 'In Transit',
        'in_transit_description' => 'The shipment is currently en route.',
        'out_for_delivery' => 'Out for Delivery',
        'out_for_delivery_description' => 'The shipment is currently on its last mile and is en route to the destination address.',
        'pre_transit' => 'Pre-Transit',
        'pre_transit_description' => 'The shipment information has been sent to the carrier. This does not indicate physical possession of the package.',
        'return_to_sender' => 'Return to Sender',
        'return_to_sender_description' => 'The shipment was not successfully delivered and is en route back to the sender.',
        'unknown' => 'Unknown',
        'unknown_description' => "The carrier doesn't know.",
    ],
];
