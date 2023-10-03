<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Events\Webhooks\InvalidWebhookSignatureEvent;
use CybrixSolutions\EasyPost\Models\WebhookCall;
use CybrixSolutions\EasyPost\Tests\Fixtures\Webhooks\CustomRespondsToWebhook;
use CybrixSolutions\EasyPost\Tests\Fixtures\Webhooks\ProcessNothingWebhookProfile;
use CybrixSolutions\EasyPost\Tests\Fixtures\Webhooks\WebhookModelWithoutPayloadSaved;
use CybrixSolutions\EasyPost\Tests\TestConcerns\UsesDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\postJson;

uses(UsesDatabase::class);

beforeEach(function () {
    config()->set('easypost.webhook_secret', 'abc123');
    config()->set('easypost.webhook_url', '/webhooks/easypost');

    Queue::fake();

    Event::fake();

    $this->payload = [
        'object' => 'Event',
        'description' => 'tracker.created',
        'mode' => 'test',
        'result' => [],
    ];

    $this->headers = [
        'X-Hmac-Signature' => makeWebhookSignature($this->payload),
    ];
});

it('can process a webhook request', function () {
    $this->withoutExceptionHandling();

    postJson('/webhooks/easypost', $this->payload, $this->headers)
        ->assertSuccessful();

    $this->assertDatabaseCount(WebhookCall::class, 1);

    $webhookCall = WebhookCall::first();
    expect($webhookCall)->name->toBe('tracker.created')
        ->and($webhookCall)->payload->toMatchArray([
            'object' => 'Event',
            'description' => 'tracker.created',
            'mode' => 'test',
            'result' => [],
        ]);
});

it('will not process a webhook with an invalid payload', function () {
    $headers = $this->headers;
    $headers['X-Hmac-Signature'] .= 'invalid';

    postJson('/webhooks/easypost', $this->payload, $headers)
        ->assertServerError();

    $this->assertDatabaseCount(WebhookCall::class, 0);
    Queue::assertNothingPushed();
    Event::assertDispatched(InvalidWebhookSignatureEvent::class);
});

it('can work with an alternate profile', function () {
    config()->set('easypost.webhook_config.profile', ProcessNothingWebhookProfile::class);

    postJson('/webhooks/easypost', $this->payload, $this->headers)
        ->assertSuccessful();

    Queue::assertNothingPushed();
    Event::assertNotDispatched(InvalidWebhookSignatureEvent::class);

    $this->assertDatabaseCount(WebhookCall::class, 0);
});

it('can work with an alternate model', function () {
    $this->withoutExceptionHandling();

    config()->set('easypost.models.webhook_call', WebhookModelWithoutPayloadSaved::class);

    postJson('/webhooks/easypost', $this->payload, $this->headers)
        ->assertSuccessful();

    $this->assertDatabaseCount(WebhookCall::class, 1);

    expect(WebhookCall::first()->payload)->toBe([]);
});

it('can respond with a custom response', function () {
    config()->set('easypost.webhook_config.response', CustomRespondsToWebhook::class);

    postJson('/webhooks/easypost', $this->payload, $this->headers)
        ->assertSuccessful()
        ->assertJson([
            'foo' => 'bar',
        ]);
});

it('can store a specific header', function () {
    $this->withoutExceptionHandling();

    config()->set('easypost.webhook_config.store_headers', ['X-Hmac-Signature']);

    postJson('/webhooks/easypost', $this->payload, $this->headers)
        ->assertSuccessful();

    $this->assertDatabaseCount(WebhookCall::class, 1);

    $webhookCall = WebhookCall::first();

    expect($webhookCall->headers)->toHaveCount(1)
        ->and($webhookCall->headerBag()->get('X-Hmac-Signature'))->toEqual($this->headers['X-Hmac-Signature']);
});

it('can store all of the headers', function () {
    $this->withoutExceptionHandling();

    config()->set('easypost.webhook_config.store_headers', '*');

    postJson('/webhooks/easypost', $this->payload, $this->headers)
        ->assertSuccessful();

    $this->assertDatabaseCount(WebhookCall::class, 1);

    expect(count(WebhookCall::first()->headers))->toBeGreaterThan(1);
});

it('can store none of the headers', function () {
    $this->withoutExceptionHandling();

    config()->set('easypost.webhook_config.store_headers', []);

    postJson('/webhooks/easypost', $this->payload, $this->headers)
        ->assertSuccessful();

    $this->assertDatabaseCount(WebhookCall::class, 1);

    expect(WebhookCall::first()->headers)->toHaveCount(0);
});

// Helpers

function makeWebhookSignature(array $payload): string
{
    $secret = config('easypost.webhook_secret');

    $signature = hash_hmac('sha256', json_encode($payload), $secret);

    return "hmac-sha256-hex={$signature}";
}
