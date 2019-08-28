<?php

namespace App\Traits\Http;

use App\Enums\ErrorCode;
use App\Fractal\Serializer\ApiSerializer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use League\Fractal\Serializer\SerializerAbstract;
use Spatie\Fractalistic\Fractal;

trait ApiResponse
{
    protected function defaultSerializer(): SerializerAbstract
    {
        return app(ApiSerializer::class);
    }

    protected function normalizeData($data): array
    {
        if (\is_array($data)) {
            return $data;
        }

        if ($data instanceof Fractal) {
            $data = $data->serializeWith($this->defaultSerializer());
        }

        if ($data instanceof \JsonSerializable) {
            return $data->jsonSerialize();
        }

        throw new \InvalidArgumentException('Data is not serializable');
    }

    protected function responseJson($data, int $httpCode = Response::HTTP_OK): JsonResponse
    {
        $data = $this->normalizeData($data);

        return response()->json($data, $httpCode);
    }

    /**
     * @param array|\JsonSerializable $collection
     * @param int|null                $count
     * @param int|null                $httpCode
     *
     * @return JsonResponse
     */
    protected function responseJsonCollection(
        $collection,
        int $count = null,
        int $httpCode = Response::HTTP_OK
    ): JsonResponse {
        $collection = $this->normalizeData($collection);
        $response = $this->responseJson([
            'count' => $count ?? \count($collection),
            'items' => $collection,
        ], $httpCode);

        return $response;
    }

    protected function responseError(
        int $httpCode,
        string $message = null,
        int $errorCode = null,
        array $errorStack = []
    ): JsonResponse {
        $json = array_filter([
            'code' => $errorCode ?: $httpCode,
            'message' => $message ?: Response::$statusTexts[$httpCode] ?? null,
            'errors' => $errorStack,
        ]);

        return response()->json($json, $httpCode);
    }

    protected function responseOk(): Response
    {
        return response()->make(null, Response::HTTP_NO_CONTENT);
    }

    protected function badRequest(string $message, int $errorCode = null): JsonResponse
    {
        return $this->responseError(Response::HTTP_BAD_REQUEST, $message, $errorCode);
    }

    protected function notFound(string $message = null): JsonResponse
    {
        return $this->responseError(Response::HTTP_NOT_FOUND, $message);
    }

    protected function serverError(string $message = null, int $errorCode = ErrorCode::UNKNOWN_ERROR): JsonResponse
    {
        //@todo add correct message
        $message = $message ?? __('Some error happened');

        return $this->responseError(Response::HTTP_BAD_REQUEST, $message, $errorCode);
    }
}
