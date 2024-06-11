<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Filament\Actions;

use Filament\Actions\Action;
use Filament\Support\Facades\FilamentIcon;
use Livewire\Component;

class BackToCarrierSearchAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('easypost::actions.back_to_carrier_search.label'));

        $this->link();

        $this->color('gray');

        $this->icon(FilamentIcon::resolve('easypost::back-arrow') ?? 'heroicon-m-arrow-left');

        $this->action(function (Component $livewire) {
            $livewire->selectedCarrierType = null;
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'backToCarrierSearch';
    }
}
