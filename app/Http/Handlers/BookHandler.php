<?php

namespace App\Http\Handlers;

use App\Contracts\Repositories\BookRepository;
use App\Helpers\UploadHelper;
use Illuminate\Http\UploadedFile;

class BookHandler
{
    protected $repository;

    public function __construct(BookRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handleCreate(array $data)
    {
        if (isset($data['covers']) && $data['covers'] instanceof UploadedFile) {
            $data['covers'] = UploadHelper::uploadFile($data['covers'], 'books/covers');
        }

        return $this->repository->store($data);
    }

    public function handleUpdate(mixed $id, array $data)
    {
        $book = $this->repository->show($id);

        if (isset($data['covers']) && $data['covers'] instanceof UploadedFile) {
            if ($book->covers) {
                UploadHelper::deleteFile($book->covers);
            }
            $data['covers'] = UploadHelper::uploadFile($data['covers'], 'books/covers');
        }

        $this->repository->update($id, $data);        
        return $this->repository->show($id);
    }

    public function handleDelete(mixed $id)
    {
        $book = $this->repository->show($id);

        if ($book->covers) {
            UploadHelper::deleteFile($book->covers);
        }

        return $this->repository->delete($id);
    }
}