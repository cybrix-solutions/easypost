<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Services;

use CybrixSolutions\EasyPost\Exceptions\CarrierAccounts\CarrierAccountCreationFailed;
use CybrixSolutions\EasyPost\Exceptions\CarrierAccounts\CarrierAccountDeletionFailed;
use CybrixSolutions\EasyPost\Exceptions\CarrierAccounts\CarrierAccountRetrievalFailed;
use CybrixSolutions\EasyPost\Exceptions\CarrierAccounts\CarrierAccountUpdateFailed;
use CybrixSolutions\EasyPost\Services\Api\ProductionEasyPostClient;
use EasyPost\CarrierAccount;
use EasyPost\Exception\Api\ApiException;
use EasyPost\Exception\Api\NotFoundException;

final readonly class CarrierAccountService
{
    public function __construct(protected ProductionEasyPostClient $api)
    {
    }

    /**
     * @return array<int, \EasyPost\CarrierAccount>
     *
     * @throws \EasyPost\Exception\Api\ApiException
     */
    public function all(): array
    {
        return $this->api->carrierAccount->all();
    }

    public function create(string $type, string $name, array $data, string $reference = null): CarrierAccount
    {
        try {
            return $this->api->carrierAccount->create(array_filter([
                'type' => $type,
                'reference' => $reference,
                'description' => $name,
                ...$data,
            ]));
        } catch (ApiException $e) {
            $message = $e->getMessage();

            if (count($e->errors ?? [])) {
                $message .= ' - ' . $e->errors[0]['message'] ?? 'Unknown error';
            }

            throw CarrierAccountCreationFailed::because($message);
        }
    }

    public function find(string $id): CarrierAccount
    {
        try {
            return $this->api->carrierAccount->retrieve($id);
        } catch (NotFoundException $e) {
            throw CarrierAccountRetrievalFailed::notFound($e->getMessage());
        } catch (ApiException $e) {
            throw CarrierAccountRetrievalFailed::generalError($e->getMessage());
        }
    }

    public function update(string $id, array $data): CarrierAccount
    {
        try {
            $account = $this->find($id);

            return $this->api->carrierAccount->update($account->id, $data);
        } catch (CarrierAccountRetrievalFailed|ApiException $e) {
            throw CarrierAccountUpdateFailed::because($e->getMessage());
        }
    }

    public function destroy(string $id): bool
    {
        try {
            $account = $this->find($id);

            $this->api->carrierAccount->delete($account->id);

            return true;
        } catch (CarrierAccountRetrievalFailed|ApiException $e) {
            throw CarrierAccountDeletionFailed::because($e->getMessage());
        }
    }
}
