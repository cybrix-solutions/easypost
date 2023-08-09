<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Exceptions\Shipments;

use CybrixSolutions\EasyPost\Enums\ShipmentOptions\LabelFormat;
use Exception;

final class InvalidLabelFormat extends Exception
{
    public static function invalid(string $format): self
    {
        $supportedFormats = implode(', ', LabelFormat::values());

        return new self("The label format `{$format}` is invalid. Format must be one of the following: {$supportedFormats}");
    }
}
