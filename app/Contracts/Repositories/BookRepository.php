<?php

namespace App\Contracts\Repositories;

use App\Contracts\Interfaces\BookInterface;
use App\Helpers\QueryFilterHelper;
use App\Models\Book;

class BookRepository extends BaseRepository implements BookInterface
{
    public function __construct(Book $model)
    {
        $this->model = $model;
    }

    public function getAllBooks(array $filters, int $perPage = 10)
    {
        $query = $this->model->newQuery();
        $searchColumns = ['author', 'title', 'publisher', 'year', 'isbn'];

        QueryFilterHelper::applyFilters($query, $filters, $searchColumns);
        QueryFilterHelper::applySorting($query, $filters);

        return $query->paginate($perPage);
    }

    public function slug(string $slug)
    {
        return $this->model->with('title')->where('slug', $slug)->firstOrFail();
    }
}