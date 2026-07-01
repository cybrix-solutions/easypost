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
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component as SchemaComponent;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\ArrayRecord;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
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
class WebhookManager extends Component implements HasActions, HasSchemas, HasTable
{
    use AuthorizesRequests;
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

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

    public function render(): string
    {
        return <<<'HTML'
        <div>
            {{ $this->content }}

            <x-filament-actions::modals />
        </div>
        HTML;
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('easypost::livewire/webhooks.heading'))
                    ->description(__('easypost::livewire/webhooks.description'))
                    ->schema([
                        Text::make($this->missingProductionWebhookWarning())
                            ->extraAttributes(['class' => 'w-full'])
                            ->hidden(fn (): bool => $this->hasProductionWebhook),
                        EmbeddedTable::make(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (): array => $this->webhookRecords())
            ->queryStringIdentifier('webhooks')
            ->columns([
                TextColumn::make('url')
                    ->label(__('easypost::livewire/webhooks.table.url.label'))
                    ->limit(70)
                    ->tooltip(fn (array $record): string => $record['url'])
                    ->description(fn (array $record): string => __('easypost::livewire/webhooks.table.id.label', ['id' => $record['id']])),
                TextColumn::make('mode')
                    ->label(__('easypost::livewire/webhooks.table.mode.label'))
                    ->badge()
                    ->color(fn (string $state): string => $state === 'production' ? 'success' : 'primary')
                    ->formatStateUsing(fn (string $state): string => $state === 'production'
                        ? __('easypost::livewire/webhooks.modes.production')
                        : __('easypost::livewire/webhooks.modes.test')),
            ])
            ->recordActions([
                $this->deleteWebhookAction(),
            ])
            ->headerActions([
                $this->addWebhookAction()
                    ->visible(fn (): bool => app()->isLocal()),
            ])
            ->emptyStateIcon(FilamentIcon::resolve('easypost::empty-webhooks') ?? 'heroicon-o-circle-stack')
            ->emptyStateHeading(__('easypost::livewire/webhooks.table.empty.heading'))
            ->emptyStateDescription(__('easypost::livewire/webhooks.table.empty.description'));
    }

    public function addWebhookAction(): Action
    {
        return Action::make('addWebhook')
            ->label(__('easypost::livewire/webhooks.actions.add.label'))
            ->modalSubmitActionLabel(__('easypost::livewire/webhooks.actions.add.modal_submit'))
            ->modalHeading(__('easypost::livewire/webhooks.actions.add.heading'))
            ->schema([
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
                fn (array $record) => new HtmlString(Str::inlineMarkdown(__('easypost::livewire/webhooks.actions.delete.content', ['url' => $record['url'] ?? '?'])))
            )
            ->action(function (array $record, Action $action, DeleteWebhookAction $deleteWebhookAction) {
                if (! isset($record['id'], $record['mode'])) {
                    return;
                }

                try {
                    $deleteWebhookAction(
                        webhookId: $record['id'],
                        testMode: $record['mode'] !== 'production',
                    );

                    unset($this->webhooks, $this->hasProductionWebhook);
                    $this->flushCachedTableRecords();
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

    protected function webhookUrlInput(): SchemaComponent
    {
        return TextInput::make('url')
            ->label(__('easypost::livewire/webhooks.actions.add.url.label'))
            ->placeholder(__('easypost::livewire/webhooks.actions.add.url.placeholder'))
            ->default(fn () => $this->defaultTestWebhookUrl())
            ->helperText(__('easypost::livewire/webhooks.actions.add.url.helper_text'))
            ->required();
    }

    protected function missingProductionWebhookWarning(): HtmlString
    {
        return new HtmlString(Blade::render(<<<'HTML'
            <div class="rounded-md bg-danger-50 px-4 py-6 dark:bg-danger-500/10">
                <div class="text-sm text-danger-700 dark:font-semibold dark:text-white">
                    <p>
                        {{ __('easypost::livewire/webhooks.production.missing.description') }}
                    </p>

                    <div class="mt-3">
                        {{ $configureProductionWebhookAction }}
                    </div>
                </div>
            </div>
            HTML, [
            'configureProductionWebhookAction' => $this->configureProductionWebhookAction,
        ]));
    }

    protected function defaultTestWebhookUrl(): string
    {
        $domain = rtrim(request()->getSchemeAndHttpHost() ?? config('app.url'), '/');
        $path = ltrim(config('easypost.webhook_url'), '/');

        return Str::of("{$domain}/{$path}")
            ->lower()
            ->toString();
    }

    protected function webhookRecords(): array
    {
        return $this->webhooks
            ->map(fn (EasyPostWebhook $webhook): array => [
                ArrayRecord::getKeyName() => $webhook->id,
                'id' => $webhook->id,
                'url' => $webhook->url,
                'mode' => $webhook->mode,
            ])
            ->values()
            ->all();
    }

    protected function findWebhook(string $id): ?EasyPostWebhook
    {
        return $this->webhooks->firstWhere(fn (EasyPostWebhook $webhook) => $webhook->id === $id);
    }
}
