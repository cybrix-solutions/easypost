<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\database\factories;

use CybrixSolutions\EasyPost\Tests\Fixtures\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\CybrixSolutions\EasyPost\Tests\Fixtures\Models\User>
 */
final class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail,
            'password' => 'secret',
        ];
    }

    public function notAllowed(): self
    {
        return $this->state(['email' => 'not-allowed@example.com']);
    }
}
