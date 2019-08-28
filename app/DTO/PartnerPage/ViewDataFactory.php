<?php

namespace App\DTO\PartnerPage;

use App\Models\Action;
use App\Models\Partner;
use App\Services\Fiat\FiatService;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Request;

class ViewDataFactory
{
    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var \HCustomizations
     */
    protected $customizationHelper;

    /**
     * @var Request|\Request
     */
    protected $request;

    /**
     * @var FiatService
     */
    protected $fiatService;

    public function __construct(
        UrlGenerator $urlGenerator,
        \HCustomizations $customizationHelper,
        Request $request,
        FiatService $fiatService
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->customizationHelper = $customizationHelper;
        $this->request = $request;
        $this->fiatService = $fiatService;
    }

    public function factory(Partner $partner, string $overriddenTitle = null): ViewData
    {
        $viewData = new ViewData();
        $viewData->partner = $partner;
        $viewData->cabinetTitle = $this->customizationHelper::clientAppTitle($partner, $overriddenTitle);
        $viewData->earnMessage = $partner->getSetting(Partner::SETTINGS_EARN_MESSAGE, ($partner->isBitrewardsEnabled() ? __('Earn BIT') : __('Earn points')));
        $viewData->spendMessage = $partner->getSetting(Partner::SETTINGS_SPEND_MESSAGE, ($partner->isBitrewardsEnabled() ? __('Spend BIT') : __('Spend points')));
        $viewData->earnTipMessage = $partner->getSetting(Partner::SETTINGS_EARN_TIP_MESSAGE);
        $viewData->spendTipMessage = $partner->getSetting(Partner::SETTINGS_SPEND_TIP_MESSAGE);

        $viewData->discountInsteadOfLoyaltyMessage = $this->customizationHelper::discountInsteadOfLoyalty($partner)
            ? __('Activate discount card')
            : __('Activate loyalty card');
        $viewData->rewardNAmountMessage = $partner->isBitrewardsEnabled() ? __('+N BIT') : __('+N points');
        $viewData->activatePlasticBeforeOtherActions = $this->customizationHelper::activatePlasticBeforeOtherActions($partner);
        $viewData->emailConfirmationUrl = $this->generateUrl('client.sendEmailConfirmation', $partner);
        $viewData->provideEmailUrl = $this->generateUrl('client.provideEmail', $partner);
        $viewData->logoutUrl = $this->generateUrl('client.logout', $partner);
        $viewData->resetPasswordEmailOrPhone = $this->request->emailOrPhone ?? null;
        $viewData->resetPasswordConfirmToken = $this->request->token ?? null;
        $viewData->bitToEthExchangeRate = number_format($this->fiatService->getExchangeRate('BIT', 'ETH'), 5, '.', '');
        $viewData->ethToBitExchangeRate = number_format($this->fiatService->getExchangeRate('ETH', 'BIT'), 0, '.', '');
        $viewData->updateEthWalletAddressUrl = $this->generateUrl('client.reward.updateEthWalletAddress', $partner);

        $viewData->isEarnBitHidden = $partner->getSetting(Partner::SETTINGS_IS_EARN_BIT_HIDDEN, false);
        $viewData->isSpendBitHidden = $partner->getSetting(Partner::SETTINGS_IS_SPEND_BIT_HIDDEN, false);
        $viewData->isInviteFriendsHidden = $partner->getSetting(Partner::SETTINGS_IS_INVITE_FRIENDS_HIDDEN, false);
        $viewData->isActivatePlasticHidden = $partner->getSetting(Partner::SETTINGS_IS_ACTIVATE_PLASTIC_HIDDEN, false);
        $viewData->isEnterPromocodeHidden = $partner->getSetting(Partner::SETTINGS_IS_ENTER_PROMOCODE_HIDDEN, false);
        $viewData->isMyCouponsHidden = $partner->getSetting(Partner::SETTINGS_IS_MY_COUPONS_HIDDEN, false);
        $viewData->isLogoutButtonHidden = $partner->getSetting(Partner::SETTINGS_IS_LOGOUT_BUTTON_HIDDEN, false);
        $viewData->isPopupCloseButtonHidden = $partner->getSetting(Partner::SETTINGS_IS_POPUP_CLOSE_BUTTON_HIDDEN, false);
        $viewData->isEditProfileButtonHidden = $partner->getSetting(Partner::SETTINGS_IS_EDIT_PROFILE_BUTTON_HIDDEN, false);
        $viewData->isClientReferralHeadingHidden = $partner->getSetting(Partner::SETTINGS_IS_EDIT_PROFILE_BUTTON_HIDDEN, false);
        $viewData->isWithdrawDisabled = $partner->isWithdrawDisabled();

        $mode = $this->getCustomSocialActionMode($partner);
        $viewData->customSocialActionHasImage = 'image' === $mode || 'both' === $mode;
        $viewData->customSocialActionHasUrl = 'url' === $mode || 'both' === $mode;

        return $viewData;
    }

    private function getCustomSocialActionMode(Partner $partner): string
    {
        $action = $partner->actions()
            ->where('type', Action::TYPE_CUSTOM_SOCIAL_ACTION)
            ->first();

        $mode = $action->config['mode'] ?? 'both';

        if (!in_array($mode, ['both', 'image', 'url'])) {
            $mode = 'both';
        }

        return $mode;
    }

    protected function generateUrl(string $routeName, Partner $partner): string
    {
        return $this
            ->urlGenerator
            ->route($routeName, [
                'partner' => $partner->key,
            ]);
    }
}
