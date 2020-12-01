@extends( 'layout.dashboard' )

@section( 'layout.dashboard.body.with-header' )
    <div id="cards" class="flex -mx-4">
        <div class="px-4 w-1/3">
            <div class="rounded-lg p-3 bg-gradient-to-br from-blue-400 to-purple-400">
                <h3 class="text-white font-semibold">{{ __( 'Created Stores' ) }}</h3>
                <h2 class="text-white font-bold text-4xl">5</h2>
            </div>
        </div>
        <div class="px-4 w-1/3">
            <div class="rounded-lg p-3 bg-gradient-to-br from-red-400 to-purple-400">
                <h3 class="text-white font-semibold">{{ __( 'Total Users' ) }}</h3>
                <h2 class="text-white font-bold text-4xl">154</h2>
            </div>
        </div>
    </div>
@endsection