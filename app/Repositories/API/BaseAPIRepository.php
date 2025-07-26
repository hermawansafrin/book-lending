<?php

namespace App\Repositories\API;

use App\Traits\ReceiverResponseTrait;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * Base repository for handling remote api
 */
abstract class BaseAPIRepository
{
    use ReceiverResponseTrait;

    /**
     * Base url for remote api
     */
    protected ?string $baseUrl = null;

    /**
     * Timeout for remote api
     */
    protected int $timeout = 25;

    /**
     * Get data from remote api
     */
    protected function getData(string $endpoint, array $payload, bool $withVerify, ?array $options = []): array
    {
        try {
            $response = Http::timeout($this->timeout);
            if ($withVerify === false) {
                $response->withoutVerifying();
            }
            $response = $response->get($this->baseUrl.$endpoint, $payload);

            return $this->formatResponse($response);

        } catch (ConnectionException $e) {
            throw new \Exception('Connection error: '.$e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('Error: '.$e->getMessage());
        }

    }
}
