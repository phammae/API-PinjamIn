<?php

namespace App\Http\Handlers;

use App\Contracts\Repositories\MovieRepository;
use App\Helpers\UploadHelper;
use Illuminate\Http\UploadedFile;

class MovieHandler
{
    protected $repository;

    public function __construct(MovieRepository $repository)
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
        $movie = $this->repository->show($id);

        if (isset($data['covers']) && $data['covers'] instanceof UploadedFile) {
            if ($movie->covers) {
                UploadHelper::deleteFile($movie->covers);
            }
            $data['covers'] = UploadHelper::uploadFile($data['covers'], 'books/covers');
        }

        $this->repository->update($id, $data);        
        return $this->repository->show($id);
    }

    public function handleDelete(mixed $id)
    {
        $movie = $this->repository->show($id);

        if ($movie->covers) {
            UploadHelper::deleteFile($movie->covers);
        }

        return $this->repository->delete($id);
    }
}