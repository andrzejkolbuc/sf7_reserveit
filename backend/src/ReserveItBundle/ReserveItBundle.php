<?php

namespace App\ReserveItBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ReserveItBundle extends Bundle
{
    public function getPath(): string
    {
        return __DIR__;
    }
}
