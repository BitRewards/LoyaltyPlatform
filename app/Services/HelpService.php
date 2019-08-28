<?php

namespace App\Services;

use App\Models\HelpItem;
use App\Models\Partner;
use Illuminate\Support\Collection;

class HelpService
{
    /**
     * @var \HLanguage
     */
    private $hLanguage;

    public function __construct(\HLanguage $hLanguage)
    {
        $this->hLanguage = $hLanguage;
    }

    /**
     * Create default questions & answers for new Partner.
     *
     * @param Partner $partner
     */
    public function createDefaultQuestions(Partner $partner)
    {
        $current = $this->hLanguage::getCurrent();

        foreach (['ru', 'en'] as $language) {
            $this->hLanguage::setLanguage($language);

            $partner->helpItems()->create([
                'language' => $language,
                'question' => __('How does it work?'),
                'answer' => __('Our rewards program is created for you to get more value and enjoyment - earn points, which you can use to make purchases and get presents').
                    "\n\n"
                    .__('In the section "Earn points" you can see how many points you will get for your purchases, friend invitation and other actions. You can use the scores you received in the "Spend points" section, where you will also see all available offers and prizes'),
                'position' => 100,
            ]);

            $partner->helpItems()->create([
                'language' => $language,
                'question' => __('How to activate plastic loyalty card?'),
                'answer' => __('Click on your picture on the upper left, or go to "Earn points" — both places have "Add loyalty card" button.')
                        ."\n\n"
                        .__('After you add your loyalty card, it will be connected to your account and any action in our loyalty system will reflect both online and on plastic card'),
                'position' => 200,
            ]);
        }

        $this->hLanguage::setLanguage($current);
    }

    /**
     * Get help items for Partner & given language.
     *
     * @param Partner     $partner
     * @param string|null $language
     *
     * @return \Illuminate\Database\Eloquent\Collection|HelpItem[]
     */
    public function helpItemsForLanguage(Partner $partner, string $language = null): Collection
    {
        $language = $language ?? $this->hLanguage::getCurrent();

        return $partner->helpItems()
            ->where('language', $language)
            ->orderBy('position', 'asc')
            ->get();
    }
}
