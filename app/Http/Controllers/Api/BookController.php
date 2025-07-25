<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Book\ApiCreateRequest;
use App\Http\Requests\Book\ApiPaginationRequest;
use App\Repositories\Book\BookRepository;
use Illuminate\Support\Facades\DB;

class BookController extends BaseController
{
    /** repo for book repository */
    private BookRepository $repo;

    /**
     * Constructor
     */
    public function __construct(BookRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Get books with pagination
     *
     * @OA\Get(
     *  path="/books",
     *  summary="Get books with pagination",
     *  tags={"Books"},
     *  description="Get books with pagination",
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
     *  @OA\Parameter(
     *      name="search",
     *      in="query",
     *      required=false,
     *      description="search for title or author",
     *
     *      @OA\Schema(type="sring")
     *  ),
     *
     *  @OA\Parameter(
     *      name="sort_by",
     *      in="query",
     *      required=false,
     *      description="sort data by specific column (default by title)",
     *
     *      @OA\Schema(type="string", enum={"title", "author", "available_copies", "created_at"})
     *  ),
     *
     *  @OA\Parameter(
     *      name="order_by",
     *      in="query",
     *      required=false,
     *      description="sort order (default by asc)",
     *
     *      @OA\Schema(type="string", enum={"asc", "desc"})
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
        // prepare input for repository for prevent random body request
        $input = $request->validated();
        $data = $this->repo->pagination($input);

        return $this->sendResponse($data, __('messages.retrieved'));
    }

    /**
     * Create a new book
     *
     * @OA\Post(
     *  path="/books",
     *  summary="Create a new book",
     *  tags={"Books"},
     *  security={{"Bearer":{}}},
     *  description="Create a new book",
     *
     *  @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/CreateBook")),
     *
     *  @OA\Response(
     *      response=200,
     *      description="successful operation",
     *  )
     * )
     */
    public function create(ApiCreateRequest $request)
    {
        $input = $request->validated();

        DB::beginTransaction();
        try {
            $data = $this->repo->create($input);

            DB::commit();

            return $this->sendResponse($data, __('messages.created'));
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}
