<?php

namespace App\Http\Handlers;

use App\Contracts\Repositories\BookRepository;

class BookHandler
{
    protected $repository;

    public function __construct(BookRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handleCreate(array $data)
    {
        
    }
}