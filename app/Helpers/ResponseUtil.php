<?php

namespace App\Helpers;

class ResponseUtil
{
    /**
     * Make response data and mark as success
     */
    public static function makeResponse(string $message, mixed $data): array
    {
        $return = [
            'success' => true,
            'data' => $data,
            'message' => $message,
        ];

        return $return;
    }

    /**
     * Make response data and mark as error
     */
    public static function makeError(string $message, array $data = [], array $additional = []): array
    {
        $res = [
            'success' => false,
            'data' => null,
            'message' => $message,
        ];

        if (! empty($data)) {
            $res['data'] = $data;
        }

        return $res;
    }

    /**
     * Make response data and mark as invalid
     */
    public static function makeInvalid(string $message, mixed $data): array
    {
        $return = [
            'success' => false,
            'data' => $data,
            'message' => $message,
        ];

        return $return;
    }
}
