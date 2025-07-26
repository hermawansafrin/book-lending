<?php

namespace Tests;

use App\Models\User;

trait APICallTestTrait
{
    /**
     * Call API with json response GET
     */
    public function getData(string $url, ?array $input = [], ?bool $assert = true, ?User $actingAs = null): array
    {
        return $this->callData('get', $url, $input, $assert, $actingAs);
    }

    /**
     * Call API with json response POST
     */
    public function postData(string $url, array $input, ?bool $assert = true, ?User $actingAs = null): array
    {
        return $this->callData('post', $url, $input, $assert, $actingAs);
    }

    public function callData(
        string $method,
        string $url,
        ?array $input = [],
        ?bool $assert = true,
        ?User $actingAs = null
    ) {
        // make sure its json response call API
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $this->url = url($this->baseUrl.$url); // make sure url
        $input = (is_array($input)) ? $input : json_decode($input, 1);

        if ($actingAs === null) {
            // not using actiong as
            $this->response = $this->json($method, $this->url, $input, $headers);
        } else {
            // using actiong as
            $this->response = $this->actingAs($actingAs)->json($method, $this->url, $input, $headers);
        }

        if ($assert && ! $this->dontValidateCall) {
            if ($this->response->status() !== 200) { // for debugging
                print_r($this->response->decodeResponseJson()->json());
            }
            // when assert and dontvalidatecall false, it must assert 200
            $this->response->assertStatus(200);
        }

        $returnDatas = data_get($this->response->decodeResponseJson()->json(), 'data');
        if (is_null($returnDatas)) {
            $returnDatas = $this->response->decodeResponseJson()->json();
        }

        return $returnDatas;
    }
}
