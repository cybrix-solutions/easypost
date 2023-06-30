<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\Models;

use CybrixSolutions\EasyPost\Tests\Fixtures\database\factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as BaseUser;
use Illuminate\Support\Facades\Hash;

final class User extends BaseUser
{
    use HasFactory;

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    protected static function booted(): void
    {
        // Earlier versions of Laravel 10.x don't have the "hashed" cast available, so we'll just manually
        // hash the password until we ever drop support for those earlier versions.
        self::creating(function (self $user) {
            if ($user->password) {
                $user->password = Hash::make($user->password);
            }
        });
    }
}
