<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\User\ApiMakeAdminRequest;
use App\Http\Requests\User\ApiPaginationRequest;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\DB;

class UserController extends BaseController
{
    /**
     * @var UserRepository
     */
    private $repo;

    /**
     * Constructor
     */
    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Get users with pagination (only admin can access this resources)
     *
     * @OA\Get(
     *  path="/users",
     *  summary="Get users with pagination",
     *  tags={"Users"},
     *  description="Get users with pagination",
     *  security={{"Bearer":{}}},
     *
     *  @OA\Parameter(
     *      name="per_page",
     *      in="query",
     *      required=false,
     *      description="number of items per page",
     *
     *      @OA\Schema(type="integer", default=15)
     *  ),
     *
     *  @OA\Parameter(
     *      name="page",
     *      in="query",
     *      required=false,
     *      description="page number",
     *
     *      @OA\Schema(type="integer", default=1)
     *  ),
     *
     *  @OA\Response(
     *      response=200,
     *      description="successful operation",
     *  )
     * )
     */
    public function pagination(ApiPaginationRequest $request)
    {
        $input = $request->validated();
        $data = $this->repo->pagination($input);

        return $this->sendResponse($data, __('messages.retrieved'));
    }

    /**
     * Make user as admin (only admin can access this resources)
     *
     * @OA\Post(
     *  path="/users/{id}/make-admin",
     *  summary="Make user as admin",
     *  tags={"Users"},
     *  description="Make user as admin",
     *  security={{"Bearer":{}}},
     *
     *  @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      description="user id",
     *
     *      @OA\Schema(type="integer")
     *  ),
     *
     *  @OA\Response(
     *      response=200,
     *      description="successful operation",
     *  )
     * )
     */
    public function makeAdmin(ApiMakeAdminRequest $request)
    {
        $userId = $request->route('id');

        DB::beginTransaction();
        try {
            $data = $this->repo->makeUserAsAdmin($userId);
            DB::commit();

            return $this->sendResponse($data, __('messages.updated'));
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
