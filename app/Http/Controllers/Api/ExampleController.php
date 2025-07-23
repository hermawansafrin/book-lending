<?php

namespace App\Http\Controllers\Api;

class ExampleController extends BaseController
{
    /**
     * @return Response
     *
     * @OA\Get(
     *      path="/test/check",
     *      summary="Check Summary",
     *      tags={"Example"},
     *      description="Check Description",
     *      security={{"Bearer":{}}},
     *
     *      @OA\Parameter(
     *          name="search_values",
     *          in="query",
     *          required=false,
     *
     *          @OA\Schema(type="string")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *      )
     * )
     */
    public function test() {}
}
