<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Exceptions\ParcelTracking;

use EasyPost\Exception\Api\ApiException;
use Exception;

final class ParcelTrackingFailed extends Exception
{
    private const array RetryableErrorCodes = [
        'TRACKER.RETRIEVE.ERROR',
        'TRACKER.RUN.ERROR',
    ];

    private const array RetryableHttpStatuses = [
        408,
        429,
        500,
        502,
        503,
        504,
    ];

    public static function withMessage(string $message, ?ApiException $previous = null): self
    {
        return new self(
            __('easypost::exceptions.tracking_api_fail', ['message' => $message]),
            previous: $previous,
        );
    }

    public static function cannotCreate(string $message, ?ApiException $previous = null): self
    {
        return new self(
            __('easypost::exceptions.tracking_api_create_fail', ['message' => $message]),
            previous: $previous,
        );
    }

    public function isRetryable(): bool
    {
        $previous = $this->getPrevious();
        if (! $previous instanceof ApiException) {
            return false;
        }

        return in_array($previous->getHttpStatus(), self::RetryableHttpStatuses, true)
            || in_array($previous->code, self::RetryableErrorCodes, true);
    }

    /**
     * @return array{
     *     easypost_error_code: mixed,
     *     easypost_http_status: ?int,
     * }
     */
    public function context(): array
    {
        $previous = $this->getPrevious();

        return [
            'easypost_error_code' => $previous instanceof ApiException ? $previous->code : null,
            'easypost_http_status' => $previous instanceof ApiException ? $previous->getHttpStatus() : null,
        ];
    }
}
