<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\Policies;

use CybrixSolutions\EasyPost\Contracts\CarrierAccount as CarrierAccountContract;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Authenticatable as User;

final class CarrierAccountPolicy
{
    use HandlesAuthorization;

    public function create(User $user): bool
    {
        return true;
    }

    public function edit(User $user, CarrierAccountContract $account): bool
    {
        return ! $account->isEasyPostAccount();
    }

    public function delete(User $user, CarrierAccountContract $account): bool
    {
        return true;
    }

    public function makeDefault(User $user, CarrierAccountContract $account): bool
    {
        return $account->isActive() && ! $account->default;
    }

    // Used for internal app modifications, like deactivating an account internally.
    public function modify(User $user, CarrierAccountContract $account): bool
    {
        return true;
    }

    public function sync(User $user): bool
    {
        return true;
    }
}
