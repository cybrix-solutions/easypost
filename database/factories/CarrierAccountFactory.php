<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Database\Factories;

use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Models\CarrierAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CarrierAccount>
 */
class CarrierAccountFactory extends Factory
{
    protected $model = CarrierAccount::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'easypost_id' => 'ca_' . Str::random(20),
            'billing_type' => 'carrier',
            'default' => false,
            'type' => fake()->randomElement(CarrierEnum::cases()),
        ];
    }

    public function isDefault(): self
    {
        return $this->state(['default' => true]);
    }

    public function inactive(): self
    {
        return $this->state(['deactivated_at' => fake()->dateTime]);
    }
}
