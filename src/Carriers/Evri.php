<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Evri extends Carrier
{
    protected function image(): string
    {
        return 'evri-logo.efd141242acf6faaeb932fd7443bacb0.png';
    }

    public function name(): string
    {
        return __('easypost::carriers.evri.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://hermes-df-prod.eu.auth0.com/login?state=hKFo2SBRNnY2elZyWXBiaEpKRDlUUkNQQ2tVcng3eVRzQnRQWKFupWxvZ2luo3RpZNkgaDZTNEZlVU5kMldmQ3NUQlhDV2hTN1N5ZzhxVExGa1ijY2lk2SBkQzhLMjJrZVl0UnN6SGU3ZG5tbFdTdmUya1A3bVVCRg&client=dC8K22keYtRszHe7dnmlWSve2kP7mUBF&protocol=oauth2&response_type=token%20id_token&access_type=&redirect_uri=https%3A%2F%2Fwww.evri.com%2F&scope=openid%20email&code_challenge_method=implicit&page=signUp&audience=digital-futures-apis&nonce=eJJFqqFXTk';
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.create_account');
    }
}
