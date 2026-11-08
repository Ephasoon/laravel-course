<?php

namespace App\Observers;

use App\Support\Catalog;
use Illuminate\Support\Facades\Cache;

class CatalogObserver
{
    public function created(): void
    {
        $this->clear();
    }

    public function updated(): void
    {
        $this->clear();
    }

    public function deleted(): void
    {
        $this->clear();
    }

    public function restored(): void
    {
        $this->clear();
    }

    public function forceDeleted(): void
    {
        $this->clear();
    }

    private function clear(): void
    {
        Catalog::forget();
        Cache::forget('home.latest_car_ids');
    }
}
