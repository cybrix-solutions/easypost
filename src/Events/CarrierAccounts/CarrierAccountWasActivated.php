<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Events\CarrierAccounts;

use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class CarrierAccountWasActivated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public CarrierAccount $account)
    {
    }
}
