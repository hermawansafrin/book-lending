<?php

namespace App\Traits;

use Illuminate\Http\Client\Response;

/**
 * For get external API response for same format data
 */
trait ReceiverResponseTrait
{
    /**
     * Format response from external API
     */
    public function formatResponse(Response $response): array
    {
        $host = $response->transferStats->getEffectiveUri()->getHost() ?? null;

        // init
        $statusCode = $response->status();
        $message = null;
        $data = null;

        if ($response->successful()) {
            $message = __('messages.api.success', ['host' => $host]);
            $data = $response->json();
        } elseif ($response->clientError()) {
            $message = __('messages.api.bad_request', ['host' => $host]);
            $data = $response->json() ?? null;
        } elseif ($response->serverError()) {
            $message = __('messages.api.server_error', ['host' => $host]);
        }

        return [
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data,
        ];
    }
}
