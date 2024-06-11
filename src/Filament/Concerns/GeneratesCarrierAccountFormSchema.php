<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Filament\Concerns;

use CybrixSolutions\EasyPost\Dto\EasyPostCredential;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Exceptions\CarrierAccounts\InvalidCarrierForCustomWorkflow;
use CybrixSolutions\EasyPost\Services\CarrierService;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

use function CybrixSolutions\EasyPost\setLinkTargets;

trait GeneratesCarrierAccountFormSchema
{
    protected function carrierFormSchema(?CarrierService $carrierService, bool $isCreate = true): array|Component
    {
        if (! $carrierService) {
            return [];
        }

        if ($carrierService->isCustomWorkflow()) {
            return $this->customWorkflowSchema($carrierService, $isCreate);
        }

        $productionSchema = Fieldset::make(__('easypost::labels.carrier_account_form.production_credentials'))
            ->columns(1)
            ->columnSpan(1)
            ->statePath('credentials')
            ->schema(
                $carrierService->productionCredentials()->map(
                    fn (EasyPostCredential $credential, string $credentialId) => $this->credentialToFormField(
                        credential: $credential,
                        credentialId: $credentialId,
                        testEnvironment: false,
                        isCreate: $isCreate,
                    )
                )->toArray()
            );

        $testSchema = $carrierService->hasTestCredentials()
            ? Fieldset::make(__('easypost::labels.carrier_account_form.test_credentials'))
                ->columns(1)
                ->columnSpan(1)
                ->statePath('test_credentials')
                ->schema(
                    $carrierService->testCredentials()->map(
                        fn (EasyPostCredential $credential, string $credentialId) => $this->credentialToFormField(
                            credential: $credential,
                            credentialId: $credentialId,
                            testEnvironment: true,
                            isCreate: $isCreate,
                        )
                    )->toArray()
                )
            : null;

        return Group::make(array_filter([
            $productionSchema,
            $testSchema,
        ]))
            ->columns(filled($testSchema) ? 2 : 1);
    }

    protected function credentialToFormField(
        EasyPostCredential $credential,
        string $credentialId,
        bool $testEnvironment,
        bool $isCreate,
    ): Component {
        return match (true) {
            $credential->isCheckbox() => Checkbox::make($credentialId)
                ->label(fn (): string => $credential->label())
                ->disabled(fn (): bool => $credential->isReadonly())
                ->accepted(fn (): bool => $credential->isRequired() && ! $testEnvironment)
                ->dehydrateStateUsing(fn ($state) => ! is_bool($state) ? false : $state),

            $credential->isSelect() => Select::make($credentialId)
                ->label(fn (): string => $credential->label())
                ->placeholder(__('easypost::labels.carrier_account_form.select_option_none'))
                ->required(fn (): bool => $credential->isRequired() && ! $testEnvironment)
                ->disabled(fn (): bool => $credential->isReadonly())
                ->options(fn (): array => $credential->selectOptions())
                ->selectablePlaceholder(fn (): bool => ! $credential->isRequired() || $testEnvironment)
                ->dehydrated(fn ($state): bool => filled($state))
                ->native(false),

            $credential->isPassword() => $this->getPasswordComponent($credentialId)
                ->label(fn (): string => $credential->label())
                ->hint(fn (): ?string => $isCreate ? null : __('easypost::labels.carrier_account_form.masked_field_info'))
                ->placeholder(fn (): ?string => $credential->placeholder())
                ->required(fn (): bool => $credential->isRequired() && ! $testEnvironment)
                ->readOnly(fn (): bool => $credential->isReadonly())
                ->dehydrated(fn ($state): bool => filled($state))
                ->password(),

            default => TextInput::make($credentialId)
                ->label(fn (): string => $credential->label())
                ->placeholder(fn (): ?string => $credential->placeholder())
                ->required(fn (): bool => $credential->isRequired() && ! $testEnvironment)
                ->readonly(fn (): bool => $credential->isReadonly())
                ->dehydrated(fn ($state): bool => filled($state)),
        };
    }

    protected function customWorkflowSchema(CarrierService $carrierService, bool $isCreate = true): array|Component
    {
        return match ($carrierService->carrierEnum()) {
            CarrierEnum::Fedex => $this->fedexSchema($carrierService, $isCreate),
            CarrierEnum::Ups => $this->upsSchema($carrierService, $isCreate),
            default => throw InvalidCarrierForCustomWorkflow::unsupported($carrierService->carrierEnum()->value),
        };
    }

    protected function fedexSchema(CarrierService $carrierService, bool $isCreate = true): array|Component
    {
        $customCredentials = $carrierService->customCredentials();

        return [
            $this->acceptTosInput('https://assets.easypost.com/assets/pdfs/fedex_user_reg_eula.2ea68926ba2d33b1539310012ba091fd.pdf'),
            Group::make([
                $this->makeCustomWorkflowFieldset(
                    label: __('easypost::labels.carrier_account_form.fedex.account_info'),
                    credentials: $customCredentials['credential_information'],
                    isCreate: $isCreate,
                ),

                $this->makeCustomWorkflowFieldset(
                    label: __('easypost::labels.carrier_account_form.fedex.company'),
                    credentials: $customCredentials['company_information'],
                    isCreate: $isCreate,
                ),

                $this->makeCustomWorkflowFieldset(
                    label: __('easypost::labels.carrier_account_form.fedex.address'),
                    credentials: $customCredentials['address_information'],
                    isCreate: $isCreate,
                    help: __('easypost::labels.carrier_account_form.fedex.address_help')
                ),
            ]),
        ];
    }

    protected function upsSchema(CarrierService $carrierService, bool $isCreate = true): array|Component
    {
        $customCredentials = $carrierService->customCredentials();

        return [
            $this->acceptTosInput(route('easypost::legal.ups_license')),
            Group::make([
                $this->makeCustomWorkflowFieldset(
                    label: __('easypost::labels.carrier_account_form.ups.account_info'),
                    credentials: $customCredentials['account'],
                    isCreate: $isCreate,
                    help: __('easypost::labels.carrier_account_form.ups.account_info_help'),
                ),

                $this->makeCustomWorkflowFieldset(
                    label: __('easypost::labels.carrier_account_form.ups.company'),
                    credentials: $customCredentials['company'],
                    isCreate: $isCreate,
                ),

                $this->makeCustomWorkflowFieldset(
                    label: __('easypost::labels.carrier_account_form.ups.address'),
                    credentials: $customCredentials['address'],
                    isCreate: $isCreate,
                ),
            ]),
        ];
    }

    protected function makeCustomWorkflowFieldset(
        string $label,
        Collection $credentials,
        string $statePath = 'registration_data',
        bool $isCreate = true,
        ?string $help = null,
    ): Fieldset {
        return Fieldset::make($label)
            ->statePath($statePath)
            ->columns(1)
            ->columnSpan(1)
            ->schema(array_filter([
                filled($help) ? View::make('easypost::filament.carriers.custom-workflow-help')->viewData(['help' => $help]) : null,
                ...$credentials->map(
                    fn (EasyPostCredential $credential) => $this->credentialToFormField(
                        credential: $credential,
                        credentialId: $credential->name(),
                        testEnvironment: false,
                        isCreate: $isCreate,
                    )
                )->toArray(),
            ]));
    }

    protected function acceptTosInput(string $termsUrl): Component
    {
        $labelContent = new HtmlString(
            setLinkTargets(Str::inlineMarkdown(__('easypost::labels.carrier_account_form.accept_tos.label', ['url' => $termsUrl])))
        );

        return Checkbox::make('accepted_terms')
            ->label(fn (): Htmlable => $labelContent)
            ->accepted()
            ->validationAttribute(__('easypost::labels.carrier_account_form.accept_tos.validation_label'));
    }

    protected function getPasswordComponent(string $name): Component|TextInput
    {
        /** @var class-string $component */
        $component = config('easypost.filament.password_component');

        return $component::make($name);
    }
}
