<?php

namespace App\Contracts\Interfaces\Base;


interface UpdateInterface
{
    /**
     * 
     * @param mixed $id
     * @param array $data
     * 
     * @return mixed
     */
    public function update(mixed $id, array $data): mixed;
}