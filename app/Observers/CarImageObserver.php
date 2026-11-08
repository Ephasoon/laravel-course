<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

class CarImageObserver
{
    public function created(): void
    {
        Cache::forget('home.latest_car_ids');
    }

    public function updated(): void
    {
        Cache::forget('home.latest_car_ids');
    }

    public function deleted(): void
    {
        Cache::forget('home.latest_car_ids');
    }
}
