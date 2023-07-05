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

    $this->withViewErrors([]);
});

it('renders the fields for a carrier', function () {
    setupRoute(CarrierService::fromType(CarrierEnum::Speedee));

    get('/_test')
        ->assertElementExists('div', function (AssertElement $div) {
            // Spee-dee doesn't have a test mode and has 3 input fields.
            $div->doesntHave('class', 'grid');
            $div->find('fieldset', function (AssertElement $fieldset) {
                $fieldset->contains('legend', [
                    'text' => __('easypost::labels.carrier_account_form.production_credentials'),
                ])->contains('input', 3);
            });
        });
});

it('renders test credentials if a carrier has them', function () {
    setupRoute(CarrierService::fromType(CarrierEnum::BetterTrucks));

    get('/_test')
        ->assertElementExists('div', function (AssertElement $div) {
            $div->has('class', 'grid');

            $div->contains('legend', [
                'text' => __('easypost::labels.carrier_account_form.production_credentials'),
            ])->contains('legend', [
                'text' => __('easypost::labels.carrier_account_form.test_credentials'),
            ]);
        });
});

it('renders the custom workflow for fedex', function () {
    setupRoute(CarrierService::fromType(CarrierEnum::Fedex));

    get('/_test')
        ->assertElementExists('[type="checkbox"]', function (AssertElement $checkbox) {
            $checkbox->has('name', 'accepted_terms')
                ->has('wire:model.defer', 'state.accepted_terms');
        })
        ->assertElementExists('#new-fedex-account-fields', function (AssertElement $div) {
            $div->contains('legend', [
                'text' => __('easypost::labels.carrier_account_form.fedex.account_info'),
            ])->contains('legend', [
                'text' => __('easypost::labels.carrier_account_form.fedex.company'),
            ])->contains('legend span', [
                'text' => __('easypost::labels.carrier_account_form.fedex.address'),
            ])->contains('input', [
                'name' => 'registration_data.shipping_streets',
            ]);
        });
});

it('renders the custom workflow for ups', function () {
    setupRoute(CarrierService::fromType(CarrierEnum::Ups));

    get('/_test')
        ->assertElementExists('[type="checkbox"]', function (AssertElement $checkbox) {
            $checkbox->has('name', 'accepted_terms')
                ->has('wire:model.defer', 'state.accepted_terms');
        })
        ->assertElementExists('#new-ups-account-fields', function (AssertElement $div) {
            $div->contains('legend span', [
                'text' => __('easypost::labels.carrier_account_form.ups.account_info'),
            ])->contains('legend', [
                'text' => __('easypost::labels.carrier_account_form.ups.company'),
            ])->contains('legend', [
                'text' => __('easypost::labels.carrier_account_form.ups.address'),
            ])->contains('input', [
                'name' => 'registration_data.country',
            ]);
        });
});

// Helpers

function setupRoute(CarrierService $service): void
{
    $template = <<<'HTML'
    <x-easypost::carrier-account-form-fields
        :carrier-service="$service"
     />
    HTML;

    Route::get(
        '/_test',
        fn () => test()->blade($template, [
            'service' => $service,
        ])
    );
}
