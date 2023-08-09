<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Models;

use CybrixSolutions\EasyPost\Exceptions\Webhooks\InvalidWebhookConfig;
use CybrixSolutions\EasyPost\Services\Webhooks\WebhookConfig;
use Exception;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\HeaderBag;

class WebhookCall extends Model
{
    use MassPrunable;

    protected $casts = [
        'headers' => 'array',
        'payload' => 'array',
        'exception' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->guarded[] = $this->primaryKey;
        $this->table = config('easypost.table_names.webhook_calls') ?: $this->getTable();
    }

    public static function storeWebhook(WebhookConfig $config, Request $request): self
    {
        $headers = self::headersToStore($config, $request);

        return self::create([
            'name' => $request->input('description'),
            'url' => $request->fullUrl(),
            'headers' => $headers,
            'payload' => $request->input(),
            'exception' => null,
        ]);
    }

    public static function headersToStore(WebhookConfig $config, Request $request): array
    {
        $headerNamesToStore = $config->storeHeaders;

        if ($headerNamesToStore === '*') {
            return $request->headers->all();
        }

        $headerNamesToStore = array_map(
            fn (string $headerName) => strtolower($headerName),
            $headerNamesToStore,
        );

        return collect($request->headers->all())
            ->filter(fn (array $headerValue, string $headerName) => in_array($headerName, $headerNamesToStore, true))
            ->toArray();
    }

    public function headerBag(): HeaderBag
    {
        return new HeaderBag($this->headers ?? []);
    }

    public function headers(): HeaderBag
    {
        return $this->headerBag();
    }

    public function saveException(Exception $exception): self
    {
        $this->exception = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ];

        $this->save();

        return $this;
    }

    public function clearException(): self
    {
        $this->update(['exception' => null]);

        return $this;
    }

    public function prunable()
    {
        $days = config('easypost.webhook_retention_days');

        if (! is_int($days)) {
            throw InvalidWebhookConfig::invalidPrunable($days);
        }

        return static::where('created_at', '<', now()->subDays($days));
    }
}
