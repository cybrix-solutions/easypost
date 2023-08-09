<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Fedex extends Carrier
{
    protected function image(): string
    {
        return 'fedex-logo.380cc4d39a40f4623dceaad6936f9d4d.svg';
    }

    public function name(): string
    {
        return __('easypost::carriers.fedex.name');
    }

    public function nameForTracker(): string
    {
        return 'FedEx';
    }

    public function signupHelpUrl(): ?string
    {
        return 'https://support.easypost.com/hc/en-us/articles/360041148012-FedEx-Account-Registration-Guide';
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.create_account');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.fedex.com/fcl/web/jsp/contactInfo1.jsp?appName=oadr&locale=us_en&step3URL=https%3A%2F%2Fwww.fedex.com%2Ffcl%2FExistingAccountFclStep3&afterwardsURL=https%3A%2F%2Fwww.fedex.com%2Ffcl%2Foptionhome&programIndicator=ss90705920';
    }

    public function needsTermsAccepted(): bool
    {
        return true;
    }
}
