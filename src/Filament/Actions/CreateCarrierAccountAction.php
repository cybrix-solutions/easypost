<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Filament\Actions;

use Closure;
use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\AddCarrierAccountAction;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Exceptions\CarrierAccounts\CarrierAccountCreationFailed;
use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use function CybrixSolutions\EasyPost\hasApiKey;

class CreateCarrierAccountAction extends Action
{
    use CanCustomizeProcess;

    protected string|Closure|null $reference = null;

    protected array|Closure|null $context = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record(null);

        $this->label(__('easypost::livewire/carriers.accounts.actions.create.label'));

        $this->successNotificationTitle(__('filament-actions::create.single.notifications.created.title'));

        $this->modalWidth(Width::SevenExtraLarge);

        $this->modalHeading(function (Component $livewire) {
            return $livewire->selectedCarrierType
                ? __('easypost::labels.carrier_account_form.carrier_add_title', ['name' => CarrierEnum::tryFrom($livewire->selectedCarrierType)?->label()])
                : __('easypost::livewire/carriers.accounts.actions.create.modal_heading');
        });

        $this->modalContent(function (Action $action): View {
            return view('easypost::filament.carriers.create-carrier-account', [
                'action' => $action,
            ]);
        });

        $this->modalSubmitAction(function (Action $action, Component $livewire): Action {
            return $action
                ->label(__('easypost::livewire/carriers.accounts.actions.create.modal_submit'))
                ->disabled(fn (): bool => blank($livewire->selectedCarrierType));
        });

        $this->registerModalActions([
            BackToCarrierSearchAction::make(),
        ]);

        $this->mountUsing(function (Component $livewire) {
            $livewire->carrierSearch = '';
            $livewire->selectedCarrierType = null;
            $livewire->createCarrierForm->fill();
        });

        $this->action(function (): void {
            $this->process(function (Component $livewire, AddCarrierAccountAction $addCarrierAccountAction): void {
                if (! $livewire->selectedCarrierService) {
                    return;
                }

                if (! hasApiKey()) {
                    return;
                }

                $data = $livewire->createCarrierForm->getState();

                $addCarrierAccountAction
                    ->withCarrierService($livewire->selectedCarrierService)
                    ->withContext($this->getContext())
                    ->withReference($this->getReference())
                    ->withoutValidation();

                try {
                    $addCarrierAccountAction($data);
                } catch (CarrierAccountCreationFailed $e) {
                    Notification::make()
                        ->danger()
                        ->title($e->getMessage())
                        ->persistent()
                        ->send();

                    $this->halt();
                }
            });

            $this->success();
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'createCarrierAccount';
    }

    public function withContext(array|Closure|null $context): static
    {
        $this->context = $context;

        return $this;
    }

    public function withReference(string|Closure|null $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getContext(): array
    {
        return $this->evaluate($this->context) ?? [];
    }

    public function getReference(): ?string
    {
        return $this->evaluate($this->reference);
    }
}
