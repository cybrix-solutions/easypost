<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Exceptions\Shipments;

use Exception;

final class SelectedRateNotFound extends Exception
{
    public static function make(): self
    {
        return new self(__('easypost::exceptions.selected_rate_not_found'));
    }
}
