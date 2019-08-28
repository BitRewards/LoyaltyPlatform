<?php

namespace App\Http\Controllers\Client;

use App\Http\Requests\Client\Action\CheckTransactionStatusRequest;
use App\Http\Requests\Client\Action\SaveSocialActionRequest;
use App\Models\Transaction;
use App\Services\ImageStorageService\IImageStorage;
use App\Services\StoreEntityService;
use App\Services\StoreEventService;
use Illuminate\Routing\Controller;

class ActionController extends Controller
{
    /**
     * @var StoreEventService
     */
    private $storeEventService;

    /**
     * @var StoreEntityService
     */
    private $storeEntityService;

    /**
     * @var IImageStorage
     */
    private $imageStorage;

    public function __construct(StoreEventService $storeEventService, StoreEntityService $storeEntityService, IImageStorage $imageStorage)
    {
        $this->storeEventService = $storeEventService;
        $this->storeEntityService = $storeEntityService;
        $this->imageStorage = $imageStorage;
    }

    /**
     * @param SaveSocialActionRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmShare(SaveSocialActionRequest $request)
    {
        if (true === $request->hasFile('image')) {
            $file = $request->file('image');

            if (!stristr($file->getMimeType(), 'image')) {
                return \jsonError([
                    'message' => 'The uploaded file is not an image',
                ]);
            }

            $url = $this->imageStorage->upload($file, 'uploads/social-media/');
            $request->image_url = $url;
        }

        $event = $this->storeEventService->saveEvent($request->partner, $request->getStoreEventData(), true);

        /**
         * @var Transaction
         */
        $transaction = Transaction::where('source_store_event_id', $event->id)->first();

        if (!$transaction) {
            return \jsonError([
                'message' => __('Unable to create transaction. Transaction limit exceeded?'),
            ], 1);
        }

        // Слава просил время в мс + 3c на случай если не успеет отработать storeEntities:autoFinishStatus
        $autoConfirmTime = null === $event->entity->status_auto_finishes_at ? null : ($event->entity->status_auto_finishes_at->timestamp - time()) * 1000 + 3000;

        if ($autoConfirmTime) {
            // Сценарий "проверка AI"
            return \jsonResponse([
                'reward' => $transaction->balance_change,
                'requiresModeration' => false,
                'transactionId' => $transaction->id,
                'autoConfirmTime' => $autoConfirmTime,
            ]);
        }

        // Сценарий "ручная модерация"
        return \jsonResponse([
            'reward' => $transaction->balance_change,
            'requiresModeration' => true,
        ]);
    }

    public function checkTransactionStatus(CheckTransactionStatusRequest $request)
    {
        $model = Transaction::findOrFail($request->transaction_id);

        if (Transaction::STATUS_CONFIRMED === $model->status) {
            return \jsonResponse([
                'success' => true,
                'reward' => $model->balance_change,
            ]);
        }

        return \jsonResponse([
            'success' => false,
        ]);
    }
}
