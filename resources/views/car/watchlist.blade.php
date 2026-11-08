<x-app-layout>
    <main>
        <div class="container">
            <h1 class="mb-medium">My Watchlist</h1>

            @if($favouriteCars->isEmpty())
                <p class="text-muted">Your watchlist is empty.</p>
            @else
                <div class="car-items-listing">
                    @foreach($favouriteCars as $car)
                        <x-car-item :car="$car" />
                    @endforeach
                </div>
            @endif

            {{ $favouriteCars->links() }}
        </div>
    </main>
</x-app-layout>