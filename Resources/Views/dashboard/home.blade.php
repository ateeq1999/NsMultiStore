@extends( 'layout.dashboard' )

@section( 'layout.dashboard.body.with-header' )
    <div id="cards">
        <ns-multistore-dashboard inline-template>
            <div>
                <div class="flex -mx-4 mb-4">
                    <div class="px-4 w-1/3">
                        <div class="shadow rounded-lg p-3 bg-gradient-to-br from-purple-400 to-purple-600">
                            <h3 class="text-white font-semibold">{{ __( 'Created Stores' ) }}</h3>
                            <h2 class="text-white font-bold text-4xl">5</h2>
                            <div class="w-full flex justify-end">
                                <span class="text-sm text-gray-200">Today : +0</span>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 w-1/3">
                        <div class="shadow rounded-lg p-3 bg-gradient-to-br from-blue-400 to-blue-600">
                            <h3 class="text-white font-semibold">{{ __( 'Total Users' ) }}</h3>
                            <h2 class="text-white font-bold text-4xl">154</h2>
                            <div class="w-full flex justify-end">
                                <span class="text-sm text-gray-200">Today : +0</span>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 w-1/3">
                        <div class="shadow rounded-lg p-3 bg-gradient-to-br from-green-400 to-green-600">
                            <h3 class="text-white font-semibold">{{ __( 'Income' ) }}</h3>
                            <h2 class="text-white font-bold text-4xl">154</h2>
                            <div class="w-full flex justify-end">
                                <span class="text-sm text-gray-200">Today : +0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="-mx-4 mb-4 flex-wrap">
                    <div class="px-4 w-1/2">
                        <div class="shadow rounded bg-white">
                            <div class="p-2 border-b border-gray-200">
                                <h3 class="font-semibold text-gray-700">Stores Income</h3>
                            </div>
                            <div 
                                class="border-b border-gray-200 p-2 flex justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-600">Order</h3>
                                    <div class="flex -mx-2">
                                        <div class="px-1">
                                            <h4 class="text-semibold text-xs text-gray-500">
                                                <i class="lar la-user-circle"></i>
                                                <span>1223</span>
                                            </h4>
                                        </div>
                                        <div class="divide-y-4"></div>
                                        <div class="px-1">
                                            <h4 class="text-semibold text-xs text-gray-600">
                                                <i class="las la-clock"></i> 
                                                <span>1223</span>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <h2 
                                        class="text-xl font-bold bg-gray-700"></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </ns-multistore-dashboard>
    </div>
@endsection

@section( 'layout.dashboard.footer.inject' )
    @parent
    <script>
        const multistoreDashboard   =   Vue.component( 'ns-multistore-dashboard', {
            name: 'ns-multistore-dashboard',
            mounted() {
                this.loadStores();
            },
            methods: {
                loadStores() {
                    nsHttpClient.get( '/api/nexopos/v4/multistores/stores-details' )
                        .subscribe( result => {
                            
                        })
                }
            }
        });
    </script>
@endsection