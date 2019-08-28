<?php

namespace App\Services\Api\Specification\Responses;

class EmptyResponse extends Response
{
    /**
     * Create new Response instance.
     *
     * @param string $description response description
     */
    public function __construct(string $description)
    {
        parent::__construct($description);

        $this->withStatus(204);
    }
}
