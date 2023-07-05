<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use Illuminate\Support\Facades\Route;
use function Pest\Laravel\get;
use Sinnbeck\DomAssertions\Asserts\AssertElement;

it('can be rendered', function () {
    Route::get('/_test', function () {
        $carrier = CarrierEnum::Ups;

        $template = <<<'HTML'
        <div id="app">
            <x-easypost::carrier-button :carrier="$carrier" />
        </div>
        HTML;

        return $this->blade($template, ['carrier' => $carrier]);
    });

    get('/_test')
        ->assertElementExists('#app > button', function (AssertElement $button) {
            $button->is('button')
                ->has('type', 'button')
                ->contains('span', [
                    'text' => 'UPS',
                ]);
        });
});
