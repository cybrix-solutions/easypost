<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\CustomWorkflows;

final class FedexWorkflow extends CustomWorkflow
{
    public function placeholders(): array
    {
        return [
            'corporate_first_name' => 'John',
            'corporate_last_name' => 'Doe',
            'corporate_job_title' => 'Manager',
            'corporate_company_name' => 'Company Name',
            'corporate_phone_number' => '123-123-1234',
            'corporate_email_address' => 'email@example.com',
            'corporate_streets' => '1234 Example St. Suite 123',
            'corporate_city' => 'San Francisco',
            'corporate_state' => 'CA',
            'corporate_postal_code' => '94104',
            'corporate_country_code' => 'US',
            'shipping_streets' => '1234 Example St. Suite 123',
            'shipping_city' => 'San Francisco',
            'shipping_state' => 'CA',
            'shipping_postal_code' => '94104',
            'shipping_country_code' => 'US',
        ];
    }

    public function rulesForField(string $field): array
    {
        if ($field === 'corporate_country_code' || $field === 'shipping_country_code' || $field === 'shipping_country') {
            return [
                'min:2',
                'max:2',
            ];
        }

        return [];
    }
}
