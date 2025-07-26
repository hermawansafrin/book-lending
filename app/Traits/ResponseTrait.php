<?php

namespace App\Traits;

use App\Helpers\ResponseUtil;
use Illuminate\Support\Facades\Response;

trait ResponseTrait
{
    /**
     * Send response valid
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse(mixed $result, string $message, int $code = 200)
    {
        return Response::json(ResponseUtil::makeResponse($message, $result), $code);
    }

    /**
     * Send response error
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError(string $error, int $code = 404, array $additional = [])
    {
        return Response::json(ResponseUtil::makeError($error, [], $additional), $code);
    }

    /**
     * Send response bad request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendBadRequest(mixed $result, string $message, int $code = 400)
    {
        return Response::json(ResponseUtil::makeInvalid($message, $result), $code);
    }
}
