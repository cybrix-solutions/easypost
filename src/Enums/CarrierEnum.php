<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Enums;

use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\Carrier;
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
    case DaiPost = 'DaiPostAccount';
    case DeliverIt = 'DeliverItAccount';
    case DeutschePostUk = 'DeutschePostUKAccount';
    case DhlEcs = 'DhlEcsAccount';
    case DhlExpress = 'DhlExpressAccount';
    case DhlPaket = 'DhlPaketAccount';
    case DhlParcel = 'DhlParcelAccount';
    case EpostGlobal = 'RRDonnelleyAccount';
    case Estafeta = 'EstafetaAccount';
    case Evri = 'HermesAccount';
    case Fedex = 'FedexAccount';
    case FedexCrossBorder = 'FedexCrossBorderAccount';
    case FedexMailView = 'FedexMailviewAccount';
    case FirstMile = 'FirstMileConciseAccount';
    case Gso = 'GsoAccount';
    case Hailify = 'HailifyAccount';
    case LoomisExpress = 'LoomisExpressAccount';
    case Lso = 'LsoAccount';
    case Maergo = 'XDeliveryAccount';
    case OmniParcel = 'OmniParcelAccount';
    case Ontrac = 'OntracAccount';
    case Optima = 'OptimaAccount';
    case OsmWorldwide = 'OsmWorldwideAccount';
    case ParcelForce = 'ParcelForceAccount';
    case Parcll = 'ParcllAccount';
    case PassportGlobal = 'PassportGlobalAccount';
    case Purolator = 'PurolatorAccount';
    case RoyalMail = 'RoyalMailAccount';
    case Sendle = 'SendleAccount';
    case SfExpress = 'SfExpressAccount';
    case SmartKargo = 'SmartKargoAccount';
    case Sonic = 'SonicAccount';
    case Speedee = 'SpeedeeAccount';
    case Swyft = 'SwyftAccount';
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

    public function nameForTracker(): string
    {
        return $this->carrier()->nameForTracker();
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

    /**
     * The daily rate divisor set by the carrier for calculating dimensional weights.
     */
    public function dailyRateDivisor(): int|float
    {
        return $this->carrier()->dailyRateDivisor();
    }

    public function maxRefNumberLength(): int
    {
        return $this->carrier()->maxRefNumberLength();
    }
}
