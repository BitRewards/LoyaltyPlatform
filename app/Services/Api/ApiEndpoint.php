<?php

namespace App\Services\Api;

use App\Services\Api\Specification\ApiOperation;
use App\Services\Api\Specification\Parameters\ArrayFormDataParameter;
use App\Services\Api\Specification\Parameters\BooleanFormDataParameter;
use App\Services\Api\Specification\Parameters\BooleanQueryParameter;
use App\Services\Api\Specification\Parameters\FloatFormDataParameter;
use App\Services\Api\Specification\Parameters\IntegerPathParameter;
use App\Services\Api\Specification\Parameters\StringPathParameter;
use App\Services\Api\Specification\Responses\EmptyResponse;
use Illuminate\Contracts\Support\Arrayable;
use App\Services\Api\Specification\Parameters\Parameter;
use App\Services\Api\Specification\Responses\JsonResponse;
use App\Services\Api\Specification\Parameters\StringQueryParameter;
use App\Services\Api\Specification\Parameters\IntegerQueryParameter;
use App\Services\Api\Specification\Parameters\StringFormDataParameter;
use App\Services\Api\Specification\Parameters\IntegerFormDataParameter;

abstract class ApiEndpoint implements Arrayable
{
    /**
     * Allowed endpoint methods.
     *
     * @var array
     */
    protected static $allowedMethods = [
        'get', 'post', 'put', 'delete', 'options', 'head', 'patch',
    ];

    /**
     * Get the endpoint path.
     *
     * @return string
     */
    abstract public function path();

    /**
     * Get the shared parameters.
     *
     * @return array
     */
    public function parameters()
    {
        return [];
    }

    /**
     * Get array representation of the endpoint.
     *
     * @return array
     */
    public function toArray()
    {
        $operations = [];

        // If current endpoint provides shared parameters, we'll
        // fetch and prepend them to every operation's params
        // list. This suits perfectly for path parameters.

        $sharedParameters = $this->parameters();

        foreach (static::$allowedMethods as $method) {
            if (!method_exists($this, $method)) {
                continue;
            }

            $operation = $this->$method();

            if (!$operation instanceof ApiOperation) {
                throw new \InvalidArgumentException('Result of "'.$method.'" method is not an API operation.');
            }

            if (count($sharedParameters) > 0) {
                $operation->setSharedParameters($sharedParameters);
            }

            $operations[$method] = $operation->toArray();
        }

        return $operations;
    }

    /**
     * Get urlencoded form MIME type.
     *
     * @return string
     */
    public function urlencodedForm(): string
    {
        return 'application/x-www-form-urlencoded';
    }

    /**
     * Basic string parameter handler.
     *
     * @param \App\Services\Api\Specification\Parameters\Parameter $parameter
     * @param array                                                $settings  = []
     *
     * @return \App\Services\Api\Specification\Parameters\Parameter
     */
    protected function stringParameter(Parameter $parameter, array $settings = [])
    {
        if (!empty($settings['enum'])) {
            $parameter->usingEnum($settings['enum']);
        }

        return $parameter;
    }

    /**
     * Create new FormData String parameter.
     *
     * @param string $name
     * @param string $description
     * @param array  $enum        = null
     *
     * @return \App\Services\Api\Specification\Parameters\StringFormDataParameter
     */
    public function stringInput(string $name, string $description, array $enum = null)
    {
        return $this->stringParameter(new StringFormDataParameter($name, $description), [
            'enum' => $enum,
        ]);
    }

    /**
     * Create new Query String parameter.
     *
     * @param string $name
     * @param string $description
     * @param array  $enum        = null
     *
     * @return \App\Services\Api\Specification\Parameters\StringFormDataParameter
     */
    public function stringQuery(string $name, string $description, array $enum = null)
    {
        return $this->stringParameter(new StringQueryParameter($name, $description), [
            'enum' => $enum,
        ]);
    }

    /**
     * Create new Path String parameter.
     *
     * @param string $name
     * @param string $description
     *
     * @return Parameter
     */
    public function stringPath(string $name, string $description)
    {
        return $this->stringParameter(new StringPathParameter($name, $description));
    }

    /**
     * Create new FormData Integer parameter.
     *
     * @param string $name
     * @param string $description
     *
     * @return \App\Services\Api\Specification\Parameters\IntegerFormDataParameter
     */
    public function integerInput(string $name, string $description)
    {
        return new IntegerFormDataParameter($name, $description);
    }

    /**
     * Create new FormData Integer parameter.
     *
     * @param string $name
     * @param string $description
     *
     * @return \App\Services\Api\Specification\Parameters\FloatFormDataParameter
     */
    public function floatInput(string $name, string $description)
    {
        return new FloatFormDataParameter($name, $description);
    }

    /**
     * Create new Query Integer parameter.
     *
     * @param string $name
     * @param string $description
     *
     * @return \App\Services\Api\Specification\Parameters\IntegerQueryParameter
     */
    public function integerQuery(string $name, string $description)
    {
        return new IntegerQueryParameter($name, $description);
    }

    /**
     * Create new Path Integer parameter.
     *
     * @param string $name
     * @param string $description
     *
     * @return IntegerPathParameter
     */
    public function integerPath(string $name, string $description)
    {
        return new IntegerPathParameter($name, $description);
    }

    /**
     * Create new FormData Boolean parameter.
     *
     * @param string $name
     * @param string $description
     *
     * @return BooleanFormDataParameter
     */
    public function booleanInput(string $name, string $description)
    {
        return new BooleanFormDataParameter($name, $description);
    }

    /**
     * Create new Query Boolean parameter.
     *
     * @param string $name
     * @param string $description
     *
     * @return BooleanQueryParameter
     */
    public function booleanQuery(string $name, string $description)
    {
        return new BooleanQueryParameter($name, $description);
    }

    /**
     * Create new FormData Array parameter.
     *
     * @param string $name
     * @param string $description
     * @param string $type
     *
     * @return ArrayFormDataParameter
     */
    public function arrayInput(string $name, string $description, string $type)
    {
        return (new ArrayFormDataParameter($name, $description))->usingItemsType($type);
    }

    /**
     * Create new JSON Response instance with referenced object.
     *
     * @param string     $description
     * @param string     $reference
     * @param int|string $status      = 200
     *
     * @return \App\Services\Api\Specification\Responses\JsonResponse
     */
    public function jsonItem(string $description, string $reference, $status = 200)
    {
        return (new JsonResponse($description))
            ->usingItemRef($reference)
            ->withStatus($status);
    }

    /**
     * Create new JSON Response instance with array of referenced objects.
     *
     * @param string     $description
     * @param string     $reference
     * @param int|string $status      = 200
     *
     * @return \App\Services\Api\Specification\Responses\JsonResponse
     */
    public function jsonArray(string $description, string $reference, $status = 200)
    {
        return (new JsonResponse($description))
            ->usingArrayRef($reference)
            ->withStatus($status);
    }

    /**
     * Create new JSON Response with given schema and status code.
     *
     * @param string $description
     * @param array  $schema
     * @param int    $status
     *
     * @return \App\Services\Api\Specification\Responses\JsonResponse
     */
    public function jsonSchema(string $description, array $schema, $status = 200)
    {
        return (new JsonResponse($description))
            ->usingSchema($schema)
            ->withStatus($status);
    }

    /**
     * Create new JSON Response with error schema.
     *
     * @param string $description
     * @param int    $status      = 422
     * @param array  $schema      = null
     *
     * @return JsonResponse
     */
    public function jsonError(string $description, $status = 422, array $schema = null)
    {
        if (is_null($schema)) {
            $schema = [
                'properties' => [
                    'error' => ['type' => 'string', 'required' => true],
                    'errorCode' => ['type' => 'string'],
                ],
            ];
        }

        return $this->jsonSchema($description, $schema, $status);
    }

    /**
     * Create new Empty Response instance.
     *
     * @param string     $description
     * @param int|string $status      = 204
     *
     * @return EmptyResponse
     */
    public function emptyResponse(string $description, $status = 204)
    {
        return (new EmptyResponse($description))->withStatus($status);
    }
}
