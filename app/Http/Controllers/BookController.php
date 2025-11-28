<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\BookRepository;
use App\Helpers\PaginateHelper;
use App\Helpers\ResponseHelper;
use App\Http\Handlers\BookHandler;
use App\Http\Requests\BookRequest;
use App\Http\Resources\BookResource;
use App\Resources\Books\BookPaginateResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    protected $repository;
    protected $handler;

    public function __construct(BookRepository $repository, BookHandler $handler)
    {
        $this->repository = $repository;
        $this->handler = $handler;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $filters = $request->only([
                'sort_by',
                'sort_direction',
                'search',
                'date_from',
                'date_to',
                'date'
            ]);

            $books = $this->repository->getAllBooks($filters, $perPage);

            return ResponseHelper::success(
                BookPaginateResource::make($books, PaginateHelper::getPaginate($books)),
                trans('alert.fetch_data_success'),
                pagination: true
            );
        } catch (\Throwable $th) {
            return ResponseHelper::error(message: $th->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $book = $this->handler->handleCreate($data);

            DB::commit();
            return ResponseHelper::success(
                new BookResource($book),
                trans('alert.add_success')
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(message: trans('alert.add_failed') . " => " . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $book = $this->repository->show($id);

            return ResponseHelper::success(
                new BookResource($book),
                trans('alert.fetch_data_success')
            );
        } catch (\Throwable $th) {
            return ResponseHelper::error(message: $th->getMessage());
        }
    }

    public function slug($slug)
    {
        try {
            $book = $this->repository->slug($slug);
            return ResponseHelper::success(
                new BookResource($book),
                trans('alert.fetch_data_success')
            );
        } catch (\Throwable $th) {
            return ResponseHelper::error(message: $th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookRequest $request, $id)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $book = $this->handler->handleUpdate($id, $data);

            DB::commit();
            return ResponseHelper::success(
                new BookResource($book),
                trans('alert.update_success')
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(message: trans('alert.update_failed') . " => " . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->handler->handleDelete($id);

            DB::commit();
            return ResponseHelper::success(message: trans('alert.delete_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(message: trans('alert.delete_failed') . " => " . $th->getMessage());
        }
    }
}
