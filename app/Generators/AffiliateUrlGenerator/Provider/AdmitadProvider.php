<?php

namespace App\Generators\AffiliateUrlGenerator\Provider;

use App\Generators\AffiliateUrlGenerator\ProviderInterface;
use App\Models\Action;
use App\Models\PersonInterface;

class AdmitadProvider implements ProviderInterface
{
    public function isSupported(Action $action): bool
    {
        return Action::TYPE_AFFILIATE_ACTION_ADMITAD === $action->type;
    }

    public function generate(Action $action, PersonInterface $person): ?string
    {
        $admitadOfferId = $action->config[Action::CONFIG_ADMITAD_OFFER_ID] ?? null;

        if (!$admitadOfferId) {
            $exception = new \RuntimeException("Action {$action->id} is not configured for affiliate link generation! 'admitad-offer-id' key in action config is required");
            \Log::alert($exception);

            return null;
        }

        if (!$person->getPrimaryEmail()) {
            return null;
        }

        $escapedEmail = urlencode($person->getPrimaryEmail());

        return "https://ad.admitad.com/g/$admitadOfferId/?subid4=$escapedEmail&subid2={$action->id}";
    }
}
