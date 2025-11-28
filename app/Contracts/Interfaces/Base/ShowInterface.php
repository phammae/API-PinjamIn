<?php

namespace App\Contracts\Interfaces\Base;

interface ShowInterface
{
    /**
     * 
     * @param mixed $id
     * 
     * @return mixed
     */
    public function show(mixed $id): mixed;
}