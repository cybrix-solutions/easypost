<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Dto\EasyPostCredential;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Tests\Fixtures\Responses\Carriers\CarrierCredentials;
use EasyPost\EasyPostObject;
use Illuminate\Support\Facades\Route;
use function Pest\Laravel\get;
use Sinnbeck\DomAssertions\Asserts\AssertElement;

beforeEach(function () {
    $this->withViewErrors([]);
});

it('can be rendered as a text field', function () {
    setupCarrierAccountFieldRoute(newCredential());

    get('/_test')
        ->assertElementExists('div', function (AssertElement $div) {
            $div->contains('label', [
                'for' => '__idPrefix__.__namePrefix__.my_credential',
                'text' => 'Text Credential',
            ])->contains('input', [
                'type' => 'text',
                'name' => '__namePrefix__.my_credential',
                'id' => '__idPrefix__.__namePrefix__.my_credential',
                'wire:model.defer' => 'state.__namePrefix__.my_credential',
            ]);
        });
});

it('can be rendered as a password field', function () {
    setupCarrierAccountFieldRoute(newCredential(CarrierCredentials::passwordCredential()));

    get('/_test')
        ->assertElementExists('div', function (AssertElement $div) {
            $div->contains('label', [
                'for' => '__idPrefix__.__namePrefix__.my_credential',
                'text' => 'Password Credential',
            ])->contains('input', [
                'type' => 'password',
                'name' => '__namePrefix__.my_credential',
                'id' => '__idPrefix__.__namePrefix__.my_credential',
                'wire:model.defer' => 'state.__namePrefix__.my_credential',
            ])->doesntContain('div', [
                'text' => __('easypost::labels.carrier_account_form.masked_field_info'),
            ]);
        });
});

it('can be rendered as a select field', function () {
    setupCarrierAccountFieldRoute(newCredential(CarrierCredentials::selectCredential()));

    get('/_test')
        ->assertElementExists('div', function (AssertElement $div) {
            $div->contains('label', [
                'for' => '__idPrefix__.__namePrefix__.my_credential',
                'text' => 'Select Credential',
            ])->contains('select', [
                'name' => '__namePrefix__.my_credential',
                'id' => '__idPrefix__.__namePrefix__.my_credential',
                'wire:model.defer' => 'state.__namePrefix__.my_credential',
            ]);
        });
});

it('can be rendered as a checkbox field', function () {
    setupCarrierAccountFieldRoute(newCredential(CarrierCredentials::checkboxCredential()));

    get('/_test')
        ->assertElementExists('div', function (AssertElement $div) {
            $div->contains('input', [
                'type' => 'checkbox',
                'name' => '__namePrefix__.my_credential',
                'id' => '__idPrefix__.__namePrefix__.my_credential',
                'wire:model.defer' => 'state.__namePrefix__.my_credential',
            ])->contains('label', [
                'for' => '__idPrefix__.__namePrefix__.my_credential',
                'text' => 'Checkbox Credential',
            ])->doesntContain('label', [
                'class' => 'block',
            ]);
        });
});

it('renders a masked field info for password fields when editing an account', function () {
    setupCarrierAccountFieldRoute(credential: newCredential(CarrierCredentials::passwordCredential()), isCreate: false);

    get('/_test')
        ->assertElementExists('div', function (AssertElement $div) {
            $div->contains('div', [
                'text' => __('easypost::labels.carrier_account_form.masked_field_info'),
            ]);
        });
});

it('renders a readonly attribute for readonly credentials', function () {
    setupCarrierAccountFieldRoute(newCredential(CarrierCredentials::readonlyCredential()));

    get('/_test')
        ->assertElementExists('div', function (AssertElement $div) {
            $div->find('input', function (AssertElement $input) {
                $input->has('readonly');
            });
        });
});

it('only renders the required attribute for production credentials', function () {
    setupCarrierAccountFieldRoute(newCredential(CarrierCredentials::textCredential()));

    get('/_test')
        ->assertElementExists('div', function (AssertElement $div) {
            $div->find('input', function (AssertElement $input) {
                $input->has('required');
            });
        });

    setupCarrierAccountFieldRoute(credential: newCredential(CarrierCredentials::textCredential()), isTestEnv: true);

    get('/_test')
        ->assertElementExists('div', function (AssertElement $div) {
            $div->find('input', function (AssertElement $input) {
                $input->doesntHave('required');
            });
        });
});

it('renders an error message when a field has validation errors', function () {
    setupCarrierAccountFieldRoute(newCredential(CarrierCredentials::textCredential()));

    $this->withViewErrors(['__namePrefix__.my_credential' => 'The my credential field is required.']);

    get('/_test')
        ->assertElementExists('div', function (AssertElement $div) {
            $div->contains('div', [
                'text' => 'The my credential field is required.',
            ]);
        });
});

// Helpers

function newCredential(?EasyPostObject $type = null, ?CarrierEnum $enum = null): EasyPostCredential
{
    $type ??= CarrierCredentials::textCredential();
    $enum ??= CarrierEnum::Speedee;

    return new EasyPostCredential(
        $type,
        'my_credential',
        $enum,
    );
}

function setupCarrierAccountFieldRoute(
    EasyPostCredential $credential,
    string $namePrefix = '__namePrefix__',
    string $idPrefix = '__idPrefix__',
    bool $isTestEnv = false,
    bool $isCreate = true,
): void {
    $template = <<<'HTML'
    <x-easypost::carrier-account-field
        :credential="$credential"
        credential-id="my_credential"
        :name-prefix="$namePrefix"
        :id-prefix="$idPrefix"
        :is-create="$isCreate"
        :is-test-env="$isTestEnv"
    />
    HTML;

    Route::get(
        '/_test',
        fn () => test()->blade($template, [
            'credential' => $credential,
            'namePrefix' => $namePrefix,
            'idPrefix' => $idPrefix,
            'isCreate' => $isCreate,
            'isTestEnv' => $isTestEnv,
        ])
    );
}
