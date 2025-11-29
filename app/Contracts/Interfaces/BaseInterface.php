<?php

namespace App\Contracts\Interfaces;

use App\Contracts\Interfaces\Base\GetInterface;
use App\Contracts\Interfaces\Base\ShowInterface;
use App\Contracts\Interfaces\Base\StoreInterface;
use App\Contracts\Interfaces\Base\DeleteInterface;
use App\Contracts\Interfaces\Base\UpdateInterface;


interface BaseInterface extends
    GetInterface,
    StoreInterface,
    UpdateInterface,
    ShowInterface,
    DeleteInterface {}
