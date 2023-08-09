<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\CustomWorkflows;

use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\CustomWorkflow as CustomWorkflowContract;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Exceptions\CarrierAccounts\InvalidCarrierForCustomWorkflow;
use CybrixSolutions\EasyPost\Services\CarrierService;

final class Factory
{
    protected static array $workflows = [
        CarrierEnum::Fedex->value => FedexWorkflow::class,
        CarrierEnum::Ups->value => UpsWorkflow::class,
    ];

    public static function make(CarrierService $service): CustomWorkflowContract
    {
        $workflow = self::$workflows[$service->carrierEnum()->value] ?? null;

        throw_unless($workflow, InvalidCarrierForCustomWorkflow::forCarrier($service->carrierEnum()->value));

        return new $workflow($service);
    }
}
