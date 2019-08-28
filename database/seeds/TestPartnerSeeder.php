<?php

use App\Administrator;
use App\Models\Action;
use App\Models\Partner;
use App\Models\PartnerGroup;
use App\Models\Reward;
use App\Services\UserService;
use Illuminate\Database\Seeder;

class TestPartnerSeeder extends Seeder
{
    /**
     * @var UserService
     */
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function run(): void
    {
        $partnerGroup = $this->createPartnerGroup();
        $partner = $this->createPartner($partnerGroup);
        $this->createDefaultActions($partner);
        $this->createDefaultRewards($partner);
        $this->createAdministrator($partner);
    }

    protected function createPartnerGroup(): PartnerGroup
    {
        return factory(PartnerGroup::class)->create([
            'name' => 'Brands',
        ]);
    }

    protected function createPartner(PartnerGroup $partnerGroup): Partner
    {
        return factory(Partner::class)->create([
            'key' => 'test-partner-key',
            'partner_group_id' => $partnerGroup->id,
            'title' => 'Some brand',
            'email' => 'brand@example.com',
        ]);
    }

    protected function createAdministrator(Partner $partner): Administrator
    {
        return factory(Administrator::class)->create([
            'name' => 'Адиминстратор',
            'email' => 'admin@example.com',
            'password' => $this->userService->getPasswordHash('admin'),
            'role' => Administrator::ROLE_PARTNER,
            'partner_id' => $partner->id,
            'api_token' => 'example-api-key',
        ]);
    }

    protected function createDefaultActions(Partner $partner): void
    {
        factory(Action::class)->create([
            'type' => Action::TYPE_SIGNUP,
            'partner_id' => $partner->id,
            'value' => 100,
        ]);

        factory(Action::class)->create([
            'type' => Action::TYPE_CUSTOM_BONUS,
            'partner_id' => $partner->id,
            'is_system' => true,
            'value_type' => Action::VALUE_TYPE_PERCENT,
            'value' => 100,
        ]);

        factory(Action::class)->create([
            'type' => Action::TYPE_JOIN_FB,
            'partner_id' => $partner->id,
            'value' => 50,
            'config' => [
                'url' => 'https://www.facebook.com/Giftd',
            ],
        ]);

        factory(Action::class)->create([
            'type' => Action::TYPE_JOIN_VK,
            'partner_id' => $partner->id,
            'value' => 50,
            'config' => [
                'url' => 'https://vk.com/giftd',
                'group-id' => '**REMOVED**',
            ],
        ]);

        factory(Action::class)->create([
            'type' => Action::TYPE_SHARE_VK,
            'partner_id' => $partner->id,
            'value' => 100,
        ]);

        factory(Action::class)->create([
            'type' => Action::TYPE_SHARE_FB,
            'partner_id' => $partner->id,
            'value' => 100,
        ]);

        factory(Action::class)->create([
            'type' => Action::TYPE_REFILL_BIT,
            'partner_id' => $partner->id,
        ]);

        factory(Action::class)->create([
            'type' => Action::TYPE_EXCHANGE_ETH_TO_BIT,
            'partner_id' => $partner->id,
        ]);

        factory(Action::class)->create([
            'type' => Action::TYPE_ORDER_CASHBACK,
            'partner_id' => $partner->id,
            'value_type' => Action::VALUE_TYPE_PERCENT,
            'value' => 10,
            'config' => [
                'value-policy' => [
                    [
                        'value' => 5,
                        'condition' => [
                            'minAmount' => 0,
                        ],
                        'valueType' => 'percent',
                    ],
                    [
                        'value' => 10,
                        'condition' => [
                            'minAmount' => 5000,
                        ],
                        'valueType' => 'percent',
                    ],
                ],
            ],
        ]);

        factory(Action::class, Action::TYPE_ORDER_REFERRAL)->create([
            'partner_id' => $partner->id,
        ]);
    }

    protected function createDefaultRewards(Partner $partner): void
    {
        factory(Reward::class)->create([
            'partner_id' => $partner->id,
            'price' => 200,
            'value_type' => Reward::VALUE_TYPE_PERCENT,
            'value' => 5,
            'title' => 'Discount 5%',
        ]);

        factory(Reward::class)->create([
            'partner_id' => $partner->id,
            'price' => 300,
            'value_type' => Reward::VALUE_TYPE_PERCENT,
            'value' => 10,
            'title' => 'Discount 10%',
        ]);

        factory(Reward::class)->create([
            'partner_id' => $partner->id,
            'price' => 400,
            'value_type' => Reward::VALUE_TYPE_FIXED,
            'value' => 300,
            'title' => 'Discount 300<span class="rouble-regular"></span>',
        ]);

        factory(Reward::class)->create([
            'partner_id' => $partner->id,
            'price' => 400,
            'value_type' => Reward::VALUE_TYPE_FIXED,
            'value' => 300,
            'title' => 'Discount 300<span class="rouble-regular"></span>',
            'config' => [
                'min-amount-total' => 2000,
            ],
        ]);

        factory(Reward::class)->create([
            'partner_id' => $partner->id,
            'price' => 500,
            'value_type' => Reward::VALUE_TYPE_FIXED,
            'value' => 1000,
            'title' => 'Discount 300<span class="rouble-regular"></span>',
            'config' => [
                'min-amount-total' => 5000,
            ],
        ]);

        factory(Reward::class)->create([
            'partner_id' => $partner->id,
            'price' => 2000,
            'value_type' => Reward::VALUE_TYPE_FIXED,
            'value' => 1000,
            'title' => 'Discount 1000<span class="rouble-regular"></span>',
            'config' => [
                'min-amount-total' => 5000,
            ],
        ]);

        factory(Reward::class)->create([
            'partner_id' => $partner->id,
            'price' => 3000,
            'value_type' => Reward::VALUE_TYPE_FIXED,
            'value' => 2000,
            'title' => 'Discount 2000<span class="rouble-regular"></span>',
        ]);

        factory(Reward::class)->create([
            'partner_id' => $partner->id,
            'price' => 4000,
            'value_type' => Reward::VALUE_TYPE_FIXED,
            'value' => 3000,
            'title' => 'Discount 3000<span class="rouble-regular"></span>',
        ]);

        factory(Reward::class, Reward::TYPE_BITREWARDS_PAYOUT)->create([
            'partner_id' => $partner->id,
        ]);

        factory(Reward::class, Reward::TYPE_FIAT_WITHDRAW)->create([
            'partner_id' => $partner->id,
        ]);
    }
}
