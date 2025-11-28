<?php

namespace App\Resources\Books;

use Illuminate\Http\Request;
use App\Http\Resources\BookResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BookPaginateResource extends ResourceCollection
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
            'data' => BookResource::collection($this->collection),
            'paginate' => $this->paginate,
        ];
    }
}