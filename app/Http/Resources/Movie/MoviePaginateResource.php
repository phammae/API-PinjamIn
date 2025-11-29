<?php

namespace App\Resources\Movie;

use Illuminate\Http\Request;
use App\Http\Resources\Movie\MovieResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MoviePaginateResource extends ResourceCollection
{
    protected array $paginate;

    public function __construct($resource, $paginate)
    {
        parent::__construct($resource);
        $this->paginate = $paginate;
    }

    public function toArray(Request $request)
    {
        return [
            'data' => MovieResource::collection($this->collection),
            'paginate' => $this->paginate,
        ];
    }
}