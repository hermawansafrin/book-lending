<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\ApiLoginRequest;
use App\Repositories\Auth\AuthRepository;

class AuthController extends BaseController
{
    private AuthRepository $repo;

    public function __construct(AuthRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @OA\Post(
     *  path="/login",
     *  summary="Login to system and return the token.",
     *  tags={"Authentication"},
     *  description="Do action login for access the API's",
     *
     *  @OA\RequestBody(
     *
     *      @OA\JsonContent(ref="#/components/schemas/AuthUser")
     *  ),
     *
     *  @OA\Response(
     *      response=200,
     *      description="successful operation",
     *  )
     * )
     */
    public function login(ApiLoginRequest $request)
    {
        $inputRequest = $request;
        $input = $request->validated();

        $checkIsLogin = $this->repo->doLogin($input['email'], $input['password']);

        // give the auth failed messages
        if ($checkIsLogin === false) {
            return $this->sendError(__('auth.failed'), 401);
        }

        $loginUser = $this->repo->loginUser();
        if ($loginUser === null) {
            return $this->sendError(__('auth.login_failed'), 401);
        }

        return $this->sendResponse($loginUser, __('messages.auth.login_success'));
    }
}
