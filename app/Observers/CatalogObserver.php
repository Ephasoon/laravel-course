<?php

namespace App\Observers;

use App\Support\Catalog;

class CatalogObserver
{
    public function created(): void
    {
        Catalog::forget();
    }

    public function updated(): void
    {
        Catalog::forget();
    }

    public function deleted(): void
    {
        Catalog::forget();
    }

    public function restored(): void
    {
        Catalog::forget();
    }

    public function forceDeleted(): void
    {
        Catalog::forget();
    }
}
