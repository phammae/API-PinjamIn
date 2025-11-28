<?php

namespace App\Contracts\Interfaces;

use App\Contracts\Interfaces\BaseInterface;

interface BookInterface extends BaseInterface
{
    public function getAllBooks(array $filters, int $perPage = 10);
}