<?php

namespace App\Http\Controllers;

use App\Traits\Http\ApiResponse;
use Illuminate\Http\Request;

abstract class ClientApiController
{
    use ApiResponse;

    protected function getPageParameters(Request $request): array
    {
        $params = [$request->input('page', 1)];

        if ($request->has('perPage')) {
            $params[] = $request->input('perPage');
        }

        return $params;
    }
}
