<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

class CarObserver
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
        Cache::forget('home.latest_cars');
    }
}
