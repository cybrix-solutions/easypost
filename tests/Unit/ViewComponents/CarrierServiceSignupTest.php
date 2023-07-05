<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Services\CarrierService;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts\CarrierTypesMock;
use Illuminate\Support\Facades\Route;
use function Pest\Laravel\get;
use Sinnbeck\DomAssertions\Asserts\AssertElement;

beforeEach(function () {
    mockProductionApi([
        CarrierTypesMock::make(),
    ]);
});

it('shows signup instructions if the carrier does not have a signup url', function () {
    $service = makeService(CarrierEnum::Speedee);

    Route::get('/_test', fn () => $this->blade('<x-easypost::carrier-service-signup :service="$service" />', ['service' => $service]));

    get('/_test')
        ->assertElementExists('div', function (AssertElement $div) use ($service) {
            $div->doesntContain('a')
                ->contains('p', [
                    'text' => $service->signupInstructions(),
                ]);
        });
});

it('shows a signup url if the carrier has one', function () {
    $service = makeService(CarrierEnum::BetterTrucks);

    Route::get('/_test', fn () => $this->blade('<x-easypost::carrier-service-signup :service="$service" />', ['service' => $service]));

    get('/_test')
        ->assertElementExists('div', function (AssertElement $div) use ($service) {
            $div->contains('a', [
                'href' => $service->signupUrl(),
                'text' => $service->signupInstructions(),
                'target' => '_blank',
            ]);
        });
});

it('shows a signup help url if the carrier has one', function () {
    $service = makeService(CarrierEnum::Ups);

    Route::get('/_test', fn () => $this->blade('<x-easypost::carrier-service-signup :service="$service" />', ['service' => $service]));

    get('/_test')
        ->assertElementExists('div', function (AssertElement $div) use ($service) {
            $div->contains('a', [
                'href' => $service->signupUrl(),
                'text' => $service->signupInstructions(),
                'target' => '_blank',
            ])->contains('a', [
                'href' => $service->signupHelpUrl(),
            ]);
        });
});

// Helpers

function makeService(CarrierEnum $enum): CarrierService
{
    return CarrierService::fromType($enum);
}
