<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Events;

use CybrixSolutions\EasyPost\Contracts\CarrierAccount;
use EasyPost\CarrierAccount as EasyPostCarrierAccount;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class CarrierAccountWasCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public CarrierAccount $carrierAccount, public EasyPostCarrierAccount $easyPostCarrierAccount)
    {
    }
}
