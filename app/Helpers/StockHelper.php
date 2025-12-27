<?php

namespace App\Helpers;

use App\Models\Refer;
use App\Models\Movement;
use App\Services\StockService;
use Illuminate\Support\Collection;

class StockHelper
{
    public static function increase(Refer $refer, Collection $movements): void
    {
        app(StockService::class)->increaseStock($refer, $movements);
    }

    public static function decrease(Refer $refer, Collection $movements): void
    {
        app(StockService::class)->decreaseStock($refer, $movements);
    }
}