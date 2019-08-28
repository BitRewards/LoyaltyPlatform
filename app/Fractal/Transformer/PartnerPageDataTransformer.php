<?php

namespace App\Fractal\Transformer;

use App\DTO\PartnerPageData;
use App\Fractal\Transformer\PartnerPage\ActionDataTransformer;
use App\Fractal\Transformer\PartnerPage\HelpItemDataTransformer;
use App\Fractal\Transformer\PartnerPage\PartnerDataTransformer;
use App\Fractal\Transformer\PartnerPage\RewardDataTransformer;
use App\Fractal\Transformer\PartnerPage\TransactionDataTransformer;
use App\Fractal\Transformer\PartnerPage\UserDataTransformer;
use App\Fractal\Transformer\PartnerPage\ViewDataTransformer;
use League\Fractal\TransformerAbstract;

class PartnerPageDataTransformer extends TransformerAbstract
{
    /**
     * @var PartnerDataTransformer
     */
    protected $partnerDataTransformer;

    /**
     * @var UserDataTransformer
     */
    protected $userDataTransformer;

    /**
     * @var ActionDataTransformer
     */
    protected $actionDataTransformer;

    /**
     * @var RewardDataTransformer
     */
    protected $rewardDataTransformer;

    /**
     * @var TransactionDataTransformer
     */
    protected $transactionDataTransformer;

    /**
     * @var HelpItemDataTransformer
     */
    protected $helpItemDataTransformer;

    /**
     * @var ViewDataTransformer
     */
    protected $viewDataTransformer;

    public function __construct(
        PartnerDataTransformer $partnerDataTransformer,
        UserDataTransformer $userDataTransformer,
        ActionDataTransformer $actionDataTransformer,
        RewardDataTransformer $rewardDataTransformer,
        TransactionDataTransformer $transactionDataTransformer,
        HelpItemDataTransformer $helpItemDataTransformer,
        ViewDataTransformer $viewDataTransformer
    ) {
        $this->partnerDataTransformer = $partnerDataTransformer;
        $this->userDataTransformer = $userDataTransformer;
        $this->actionDataTransformer = $actionDataTransformer;
        $this->rewardDataTransformer = $rewardDataTransformer;
        $this->transactionDataTransformer = $transactionDataTransformer;
        $this->helpItemDataTransformer = $helpItemDataTransformer;
        $this->viewDataTransformer = $viewDataTransformer;
    }

    public function transform(PartnerPageData $partnerPageData)
    {
        $data['partner'] = $this->partnerDataTransformer->transform($partnerPageData->partner);
        $data['user'] = $partnerPageData->user
            ? $this->userDataTransformer->transform($partnerPageData->user)
            : null;
        $data['actions'] = array_map([$this->actionDataTransformer, 'transform'], $partnerPageData->actions);
        $data['rewards'] = array_map([$this->rewardDataTransformer, 'transform'], $partnerPageData->rewards);

        if ($partnerPageData->user) {
            $data['transactions'] = array_map(
                [$this->transactionDataTransformer, 'transform'],
                $partnerPageData->transactions
            );
            $data['rewardTransactions'] = array_map(
                [$this->transactionDataTransformer, 'transform'],
                $partnerPageData->bitrewardsPayoutTransactions
            );
            $data['depositTransactions'] = array_map(
                [$this->transactionDataTransformer, 'transform'],
                $partnerPageData->depositTransactions
            );
        }

        $data['helpItems'] = array_map([$this->helpItemDataTransformer, 'transform'], $partnerPageData->helpItems);
        $data['viewData'] = $this->viewDataTransformer->transform($partnerPageData->viewData);

        return $data;
    }
}
