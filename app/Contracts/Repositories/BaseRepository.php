<?php

namespace App\Contracts\Repositories;

use App\Contracts\Interfaces\BaseInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseInterface
{
    /**
     * Handle model initialization
     * 
     * @var Model $model
     * 
     */

    public Model $model;

    /**
     * Handle get data
     * 
     * @return mixed
     */
    public function get(): mixed
    {
        return $this->model->query()->get();
    }

    /**
     * Handle store data
     * 
     * @param array $data
     * 
     * @return mixed
     */
    public function store(array $data): mixed
    {
        return $this->model->query()->create($data);
    }

    /**
     * Handle update data
     * 
     * @param array $data
     * @param mixed $id
     * 
     * @return mixed
     */
    public function update(mixed $id, array $data): mixed
    {
        return $this->show($id)->update($data);
    }

    /**
     * Handle show data
     * 
     * @param mixed $id
     * 
     * @return mixed $id
     */
    public function show(mixed $id): mixed
    {
        return $this->model->query()->find($id);
    }

    /**
     * Handle delete data
     * 
     * @param mixed $id
     * 
     * @return mixed
     */
    public function delete(mixed $id): mixed
    {
        return $this->show($id)->delete();
    }
}