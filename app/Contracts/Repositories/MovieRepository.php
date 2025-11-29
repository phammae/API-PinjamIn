<?php

namespace App\Contracts\Repositories;

use App\Helpers\QueryFilterHelper;
use App\Contracts\Interfaces\MovieInterface;
use App\Models\Movie;

class MovieRepository extends BaseRepository implements MovieInterface
{
    public function __construct(Movie $model)
    {
        $this->model = $model;
    }

    public function getAllBooks(array $filters, int $perPage = 10)
    {
        $query = $this->model->newQuery();
        $searchColumns = ['production_house', 'title', 'year'];

        QueryFilterHelper::applyFilters($query, $filters, $searchColumns);
        QueryFilterHelper::applySorting($query, $filters);

        return $query->paginate($perPage);
    }

    public function slug(string $slug)
    {
        return $this->model->with('title')->where('slug', $slug)->firstOrFail();
    }
}