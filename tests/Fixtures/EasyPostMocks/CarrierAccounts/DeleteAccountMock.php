<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts;

use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\EasyPostMock;

final class DeleteAccountMock extends EasyPostMock
{
    protected string $method = 'delete';

    protected string $urlPattern = '/v2\\/carrier_accounts\\/\\S*$/';

    public function forId(string $id): self
    {
        $this->urlPattern = '/v2\\/carrier_accounts\\/' . $id . '$/';

        return $this;
    }

    public function getPayload(): array
    {
        return [];
    }
}
