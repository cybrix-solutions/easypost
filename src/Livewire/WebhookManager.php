<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Livewire;

use CybrixSolutions\EasyPost\Contracts\Webhooks\AddWebhookAction;
use CybrixSolutions\EasyPost\Contracts\Webhooks\DeleteWebhookAction;
use CybrixSolutions\EasyPost\Dto\EasyPostWebhook;
use CybrixSolutions\EasyPost\Exceptions\Webhooks\WebhookCreationFailed;
use CybrixSolutions\EasyPost\Exceptions\Webhooks\WebhookDeletionFailed;
use CybrixSolutions\EasyPost\Facades\EasyPost;
use CybrixSolutions\EasyPost\Services\WebhooksService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\FilamentServiceProvider;
use Filament\Forms\Components\Component as FormComponent;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property-read Collection<int, EasyPostWebhook> $webhooks
 * @property-read bool $hasProductionWebhook
 */
class WebhookManager extends Component implements HasActions, HasForms
{
    use AuthorizesRequests;
    use InteractsWithActions;
    use InteractsWithForms;

    #[Computed]
    public function webhooks(): Collection
    {
        return app(WebhooksService::class)->all();
    }

    #[Computed]
    public function hasProductionWebhook(): bool
    {
        return $this->webhooks->filter(function (EasyPostWebhook $webhook) {
            return $webhook->mode === 'production' &&
                strtolower($webhook->url) === EasyPost::productionWebhookUrl();
        })->isNotEmpty();
    }

    public function mount(): void
    {
        throw_unless(
            class_exists(FilamentServiceProvider::class),
            new RuntimeException('Filament is required for this livewire component.'),
        );
    }

    public function render(): View
    {
        return view('easypost::livewire.webhook-manager');
    }

    public function addWebhookAction(): Action
    {
        return Action::make('addWebhook')
            ->label(__('easypost::livewire/webhooks.actions.add.label'))
            ->modalSubmitActionLabel(__('easypost::livewire/webhooks.actions.add.modal_submit'))
            ->modalHeading(__('easypost::livewire/webhooks.actions.add.heading'))
            ->form([
                $this->webhookUrlInput(),
            ])
            ->action(function (Schema $form, Action $action, AddWebhookAction $addWebhookAction) {
                abort_unless(app()->isLocal(), Response::HTTP_UNPROCESSABLE_ENTITY);

                try {
                    $addWebhookAction(
                        url: $form->getState()['url'],
                        testMode: true,
                    );

                    unset($this->webhooks, $this->hasProductionWebhook);
                } catch (WebhookCreationFailed $e) {
                    Notification::make()
                        ->danger()
                        ->title($e->getMessage())
                        ->persistent()
                        ->send();

                    $action->halt();
                }

                Notification::make()
                    ->success()
                    ->title(__('easypost::livewire/webhooks.actions.add.success'))
                    ->send();
            });
    }

    public function configureProductionWebhookAction(): Action
    {
        return Action::make('configureProductionWebhook')
            ->label(__('easypost::livewire/webhooks.actions.configure_production.label'))
            ->size(Size::Small)
            ->color('primary')
            ->action(function (AddWebhookAction $addWebhookAction, Action $action) {
                try {
                    $addWebhookAction(
                        url: EasyPost::productionWebhookUrl(),
                        testMode: false,
                    );

                    unset($this->webhooks, $this->hasProductionWebhook);
                } catch (WebhookCreationFailed $e) {
                    Notification::make()
                        ->danger()
                        ->title($e->getMessage())
                        ->send();

                    $action->halt();
                }

                Notification::make()
                    ->success()
                    ->title(__('easypost::livewire/webhooks.actions.configure_production.success'))
                    ->send();
            });
    }

    public function deleteWebhookAction(): Action
    {
        return Action::make('deleteWebhook')
            ->label(__('filament-actions::delete.single.label'))
            ->link()
            ->color('danger')
            ->requiresConfirmation()
            ->modalWidth(Width::ExtraLarge)
            ->modalSubmitActionLabel(__('filament-actions::delete.single.label'))
            ->modalHeading(__('easypost::livewire/webhooks.actions.delete.heading'))
            ->modalIcon(FilamentIcon::resolve('actions::delete-action.modal') ?? 'heroicon-o-trash')
            ->modalDescription(
                fn (array $arguments) => new HtmlString(Str::inlineMarkdown(__('easypost::livewire/webhooks.actions.delete.content', ['url' => $arguments['url'] ?? '?'])))
            )
            ->action(function (array $arguments, Action $action, DeleteWebhookAction $deleteWebhookAction) {
                $webhook = $this->findWebhook($arguments['webhook'] ?? '');
                if (! $webhook) {
                    return;
                }

                try {
                    $deleteWebhookAction(
                        webhookId: $webhook->id,
                        testMode: $webhook->mode !== 'production',
                    );
                } catch (WebhookDeletionFailed $e) {
                    Notification::make()
                        ->danger()
                        ->title($e->getMessage())
                        ->send();

                    $action->halt();
                }

                Notification::make()
                    ->success()
                    ->title(__('filament-actions::delete.single.notifications.deleted.title'))
                    ->send();
            });
    }

    protected function webhookUrlInput(): FormComponent
    {
        return TextInput::make('url')
            ->label(__('easypost::livewire/webhooks.actions.add.url.label'))
            ->placeholder(__('easypost::livewire/webhooks.actions.add.url.placeholder'))
            ->default(fn () => $this->defaultTestWebhookUrl())
            ->helperText(__('easypost::livewire/webhooks.actions.add.url.helper_text'))
            ->required();
    }

    protected function defaultTestWebhookUrl(): string
    {
        $domain = rtrim(request()->getSchemeAndHttpHost() ?? config('app.url'), '/');
        $path = ltrim(config('easypost.webhook_url'), '/');

        return Str::of("{$domain}/{$path}")
            ->lower()
            ->toString();
    }

    protected function findWebhook(string $id): ?EasyPostWebhook
    {
        return $this->webhooks->firstWhere(fn (EasyPostWebhook $webhook) => $webhook->id === $id);
    }
}
