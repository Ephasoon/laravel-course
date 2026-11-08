<?php

namespace App\View\Components;

use App\Support\Catalog;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SearchForm extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.search-form', [
            'makers' => Catalog::makers(),
            'models' => Catalog::models(),
            'states' => Catalog::states(),
            'cities' => Catalog::cities(),
            'carTypes' => Catalog::carTypes(),
            'fuelTypes' => Catalog::fuelTypes(),
        ]);
    }
}
