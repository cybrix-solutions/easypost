<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use function Pest\Laravel\get;
use Sinnbeck\DomAssertions\Asserts\AssertElement;

it('can be rendered', function () {
    $template = <<<'HTML'
    <x-easypost::carrier-field-section title="Hello world">
        <div id="my-content">Content</div>
    </x-easypost::carrier-field-section>
    HTML;

    Route::get('/_test', fn () => $this->blade($template));

    get('/_test')
        ->assertElementExists('fieldset', function (AssertElement $fieldset) {
            $fieldset->contains('legend', [
                'text' => 'Hello world',
            ])->contains('#my-content', [
                'text' => 'Content',
            ]);
        });
});

it('accepts html attributes', function () {
    $template = <<<'HTML'
    <x-easypost::carrier-field-section title="Hello world" data-foo="bar" class="foo">
        <div id="my-content">Content</div>
    </x-easypost::carrier-field-section>
    HTML;

    Route::get('/_test', fn () => $this->blade($template));

    get('/_test')
        ->assertElementExists('fieldset', function (AssertElement $fieldset) {
            $fieldset->doesntHave('title')
                ->has('data-foo', 'bar')
                ->has('class', 'foo');
        });
});

it('renders a helper description if provided one', function () {
    $template = <<<'HTML'
    <x-easypost::carrier-field-section title="Hello world" help="My help text here">
        <div id="my-content">Content</div>
    </x-easypost::carrier-field-section>
    HTML;

    Route::get('/_test', fn () => $this->blade($template));

    get('/_test')
        ->assertElementExists('fieldset', function (AssertElement $fieldset) {
            $fieldset->find('legend', function (AssertElement $legend) {
                $legend->has('x-bind:class')
                    ->contains('span', [
                        'text' => 'Hello world',
                    ])
                    ->contains('button', [
                        'type' => 'button',
                        'x-on:click' => 'showHelp = ! showHelp',
                    ]);
            });

            $fieldset->contains('p', [
                'text' => 'My help text here',
                'x-show' => 'showHelp',
            ]);

            $fieldset->contains('#my-content', [
                'text' => 'Content',
            ]);
        });
});
