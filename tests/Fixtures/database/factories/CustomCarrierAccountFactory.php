<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\database\factories;

use CybrixSolutions\EasyPost\Database\Factories\CarrierAccountFactory;
use CybrixSolutions\EasyPost\Tests\Fixtures\Models\CustomCarrierAccount;

final class CustomCarrierAccountFactory extends CarrierAccountFactory
{
    protected $model = CustomCarrierAccount::class;
}
