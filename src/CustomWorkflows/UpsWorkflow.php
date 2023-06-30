<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\CustomWorkflows;

use CybrixSolutions\EasyPost\Dto\EasyPostCredential;
use EasyPost\EasyPostObject;
use Illuminate\Support\Collection;

final class UpsWorkflow extends CustomWorkflow
{
    protected static array $fieldGroupMap = [
        'account_number' => 'account',
        'invoice_number' => 'account',
        'invoice_date' => 'account',
        'invoice_amount' => 'account',
        'invoice_currency' => 'account',
        'invoice_control_id' => 'account',

        'name' => 'company',
        'email' => 'company',
        'phone' => 'company',
        'company' => 'company',
        'website' => 'company',
        'title' => 'company',

        'street1' => 'address',
        'street2' => 'address',
        'city' => 'address',
        'state' => 'address',
        'postal_code' => 'address',
        'country' => 'address',
    ];

    protected static array $optionalFields = [
        'invoice_number',
        'invoice_date',
        'invoice_amount',
        'invoice_currency',
        'invoice_control_id',
        'street2',
        'client_ip',
    ];

    public function credentials(): Collection
    {
        return parent::credentials()['registration_data']
            ->groupBy(function (EasyPostCredential $credential, string $field): string {
                return self::$fieldGroupMap[$field] ?? 'unassigned';
            })
            ->transform(function (Collection $credentials): Collection {
                return $credentials->mapWithKeys(
                    fn (EasyPostCredential $credential): array => [$credential->name() => $credential]
                );
            });
    }

    public function fieldIsRequired(string $field, EasyPostObject $credential): bool
    {
        if (in_array($field, self::$optionalFields, true)) {
            return false;
        }

        return parent::fieldIsRequired($field, $credential);
    }

    public function placeholders(): array
    {
        return [
            'account_number' => '12A34B',
            'invoice_number' => '1234567',
            'invoice_date' => 'YYYYMMDD',
            'invoice_amount' => '100.00',
            'invoice_currency' => 'USD',
            'name' => 'John Doe',
            'email' => 'email@example.com',
            'phone' => '123-123-1234',
            'company' => 'Company Name',
            'website' => 'www.example.com',
            'title' => 'CTO',
            'street1' => '1234 Example St',
            'street2' => '2nd Fl',
            'city' => 'San Francisco',
            'state' => 'CA',
            'postal_code' => '94104',
            'country' => 'US',
        ];
    }

    public function rulesForField(string $field): array
    {
        if ($field === 'country') {
            return [
                'min:2',
                'max:2',
            ];
        }

        return [];
    }
}
