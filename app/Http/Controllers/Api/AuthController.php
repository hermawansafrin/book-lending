<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\ApiLoginRequest;
use App\Http\Requests\Auth\ApiRegisterRequest;
use App\Repositories\Auth\AuthRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\DB;

class AuthController extends BaseController
{
    /** for handling authenticated user */
    private AuthRepository $repo;

    /** for handling user data */
    private UserRepository $userRepo;

    public function __construct(AuthRepository $repo, UserRepository $userRepo)
    {
        $this->repo = $repo;
        $this->userRepo = $userRepo;
    }

    /**
     * @OA\Post(
     *  path="/register",
     *  summary="Register a new user",
     *  tags={"Authentication"},
     *  description="Register a new user",
     *
     *  @OA\RequestBody(
     *
     *      @OA\JsonContent(ref="#/components/schemas/RegisterUser")
     *  ),
     *
     *  @OA\Response(
     *      response=200,
     *      description="successful operation",
     *  )
     * )
     */
    public function register(ApiRegisterRequest $request)
    {
        $input = $request->validated();

        DB::beginTransaction();
        try {
            $data = $this->userRepo->create($input);
            DB::commit();

            return $this->sendResponse($data, __('messages.auth.register_success'));
        } catch (\Exception $e) {
            DB::rollBack();
        }

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
