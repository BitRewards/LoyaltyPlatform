<?php

namespace App\DTO\Factory;

use App\DTO\SpecialOfferActionData;
use App\Models\PersonInterface;
use App\Models\SpecialOfferAction;
use Illuminate\Support\Collection;

class SpecialOfferActionFactory
{
    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    public function __construct(ActionFactory $actionFactory)
    {
        $this->actionFactory = $actionFactory;
    }

    public function factory(SpecialOfferAction $specialOfferAction, PersonInterface $currentPerson = null): SpecialOfferActionData
    {
        return SpecialOfferActionData::make([
            'id' => $specialOfferAction->id,
            'brand' => $specialOfferAction->brand,
            'image' => $specialOfferAction->image_url,
            'action' => $this->actionFactory->factory($specialOfferAction->action, $currentPerson),
        ]);
    }

    /**
     * @param Collection      $collection
     * @param PersonInterface $currentPerson
     *
     * @return Collection|SpecialOfferActionData[]
     */
    public function factoryCollection(Collection $collection, PersonInterface $currentPerson = null): Collection
    {
        return $collection->map(function (SpecialOfferAction $specialOfferAction) use ($currentPerson) {
            return $this->factory($specialOfferAction, $currentPerson);
        });
    }
}
