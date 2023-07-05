<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Enums;

use CybrixSolutions\EasyPost\Contracts\Carrier;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

enum CarrierEnum: string
{
    case Apc = 'ApcAccount';
    case AsendiaUsa = 'AsendiaUsaAccount';
    case AustraliaPost = 'AustraliaPostAccount';
    case AxleHireV3 = 'AxlehireV3Account';
    case BetterTrucks = 'BetterTrucksAccount';
    case CanPar = 'CanparAccount';
    case ColumbusLastMile = 'ColumbusLastMileAccount';
    case CourierExpress = 'CourierExpressAccount';
    case CouriersPlease = 'CouriersPleaseAccount';
    case DeliverIt = 'DeliverItAccount';
    case DhlExpress = 'DhlExpressAccount';
    case DhlParcel = 'DhlParcelAccount';
    case Fedex = 'FedexAccount';
    case FedexCrossBorder = 'FedexCrossBorderAccount';
    case FedexMailView = 'FedexMailviewAccount';
    case LoomisExpress = 'LoomisExpressAccount';
    case Lso = 'LsoAccount';
    case Ontrac = 'OntracAccount';
    case OsmWorldwide = 'OsmWorldwideAccount';
    case Parcll = 'ParcllAccount';
    case PassportGlobal = 'PassportGlobalAccount';
    case Purolator = 'PurolatorAccount';
    case Sendle = 'SendleAccount';
    case Speedee = 'SpeedeeAccount';
    case Tforce = 'TforceConciseAccount';
    case Toll = 'TollAccount';
    case Uds = 'UdsAccount';
    case Ups = 'UpsAccount';
    case UpsIparcel = 'UpsIparcelAccount';
    case UpsMailInnovations = 'UpsMailInnovationsAccount';
    case Usps = 'UspsAccount';
    case Veho = 'VehoAccount';

    protected function carrier(): Carrier
    {
        static $cache = [];

        $class = "CybrixSolutions\\EasyPost\\Carriers\\{$this->name}";

        if (array_key_exists($class, $cache)) {
            return $cache[$class];
        }

        $instance = new $class;

        $cache[$class] = $instance;

        return $instance;
    }

    /**
     * @return \Illuminate\Support\Collection<int, self>
     */
    public static function fromSearch(string $search): Collection
    {
        $search = strtolower($search);

        return collect(self::cases())
            ->when($search, function (Collection $cases) use ($search) {
                return $cases->filter(
                    fn (self $case) => Str::contains(strtolower($case->label()), $search)
                );
            });
    }

    public function label(): string
    {
        return $this->carrier()->name();
    }

    public function companyField(): string
    {
        return $this->carrier()->companyField();
    }

    /**
     * Certain carriers, such as LSO, use the name
     * field differently.
     */
    public function nameField(): string
    {
        return $this->carrier()->nameField();
    }

    public function image(): string
    {
        return $this->carrier()->imageUrl();
    }

    /**
     * For carriers that require a custom workflow to add an account,
     * we'll provide a link to EasyPost documentation for that
     * carrier.
     */
    public function signupHelpUrl(): ?string
    {
        return $this->carrier()->signupHelpUrl();
    }

    /**
     * Some carriers, such as Spee-Dee, don't have an actual signup page,
     * so we'll provide a short instruction on how to contact the carrier
     * to create an account with them.
     */
    public function signupInstructions(): ?string
    {
        return $this->carrier()->signupInstructions();
    }

    public function signupText(): ?string
    {
        return $this->carrier()->signupText();
    }

    public function signupUrl(): ?string
    {
        return $this->carrier()->signupUrl();
    }

    public function voidableDays(): int
    {
        return $this->carrier()->voidableDays();
    }

    /**
     * Some carriers, such as FedEx, have custom workflows
     * and require a terms of service to be accepted.
     */
    public function needsTermsAccepted(): bool
    {
        return $this->carrier()->needsTermsAccepted();
    }

    /**
     * Form some reason the API doesn't return the available options for select fields,
     * so we'll just hard code them in.
     */
    public function optionsFor(string $field): array
    {
        return $this->carrier()->optionsFor($field);
    }
}
