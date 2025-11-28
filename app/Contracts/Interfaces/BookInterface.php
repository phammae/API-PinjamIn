<?php

namespace App\Contracts\Interfaces;

interface BookInterface extends BaseInterface
{
    public function getAllBooks(array $filters, int $perPage = 10);
}