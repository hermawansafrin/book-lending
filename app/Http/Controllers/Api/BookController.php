<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Book\ApiCreateRequest;
use App\Http\Requests\Book\ApiLendRequest;
use App\Http\Requests\Book\ApiPaginationRequest;
use App\Http\Requests\Book\ApiReturnRequest;
use App\Repositories\Book\BookRepository;
use App\Repositories\Loan\LoanRepository;
use Illuminate\Support\Facades\DB;

class BookController extends BaseController
{
    /** repo for book repository */
    private BookRepository $repo;

    /** repo for loan repository */
    private LoanRepository $loanRepo;

    /**
     * Constructor
     */
    public function __construct(BookRepository $repo, LoanRepository $loanRepo)
    {
        $this->repo = $repo;
        $this->loanRepo = $loanRepo;
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
     * Create a new book with 2 ways :
     *
     *      1. Create a new book with title, author, and available copies (isbn will nullable)
     *      2. Create a new book with isbn (title, author will not required anymore)
     *
     * @OA\Post(
     *  path="/books",
     *  summary="Create a new book",
     *  tags={"Books"},
     *  security={{"Bearer":{}}},
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

            throw $e;
        }
    }

    /**
     * Lend a book for authenticated user
     *
     * @OA\Post(
     *  path="/books/{id}/lend",
     *  summary="Lend a book for authenticated user",
     *  tags={"Books"},
     *  description="Lend a book for authenticated user",
     *  security={{"Bearer":{}}},
     *
     *  @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      description="book id",
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
    public function lend(ApiLendRequest $request)
    {
        $input = $request->validated();

        DB::beginTransaction();
        try {
            $data = $this->loanRepo->lend($input);

            DB::commit();

            return $this->sendResponse($data, __('messages.book.lended'));
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Return a book for authenticated user
     *
     * @OA\Post(
     *  path="/books/{id}/return",
     *  summary="Return a book for authenticated user",
     *  tags={"Books"},
     *  description="Return a book for authenticated user",
     *  security={{"Bearer":{}}},
     *
     *  @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      description="book id",
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
    public function return(ApiReturnRequest $request)
    {
        $input = $request->all(); // use all() for get $id after query user and book id for on going loan
        $validatedInput = $request->validated();

        DB::beginTransaction();
        try {
            $data = $this->loanRepo->return($input['id'], $validatedInput);

            DB::commit();

            return $this->sendResponse($data, __('messages.book.returned'));
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
