<?php

namespace App\Contracts\Interfaces\Base;

interface StoreInterface
{
    /**
     * 
     * @param array $data
     * 
     * @return mixed
     */
    public function store(array $data): mixed;
}