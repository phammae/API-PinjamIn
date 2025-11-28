<?php

namespace App\Contracts\Interfaces\Base;

interface DeleteInterface
{
    /**
     * 
     * @param mixed $id
     * 
     * @return mixed
     */
    public function delete(mixed $id): mixed;
}