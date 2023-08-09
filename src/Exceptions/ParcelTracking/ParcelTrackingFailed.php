<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Exceptions\ParcelTracking;

use Exception;

final class ParcelTrackingFailed extends Exception
{
    public static function withMessage(string $message): self
    {
        return new self(__('easypost::exceptions.tracking_api_fail', ['message' => $message]));
    }

    public static function cannotCreate(string $message): self
    {
        return new self(__('easypost::exceptions.tracking_api_create_fail', ['message' => $message]));
    }
}
