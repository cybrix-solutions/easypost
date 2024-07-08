<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\RequestData;

use CybrixSolutions\EasyPost\Enums\CarrierEnum;

final class CarrierAccountRequestData
{
    protected string $name = 'Mocked Account';

    protected array $carrierCredentials = [];

    public function __construct(protected CarrierEnum $enum) {}

    public static function make(?CarrierEnum $enum = null): self
    {
        $enum ??= CarrierEnum::Speedee;

        return new self($enum);
    }

    public static function speedeeCredentials(): array
    {
        return [
            'credentials' => [
                'account_number' => 'test',
                'ftp_username' => 'test',
                'ftp_password' => 'test',
            ],
        ];
    }

    public function usingName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function usingCarrierCredentials(array $carrierCredentials): self
    {
        $this->carrierCredentials = $carrierCredentials;

        return $this;
    }

    public function data(): array
    {
        return [
            'type' => $this->enum->value,
            'name' => $this->name,
            ...$this->carrierCredentials,
        ];
    }
}
