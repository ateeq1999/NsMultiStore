@extends( 'layout.dashboard' )

@section( 'layout.dashboard.body.with-header' )
    <div id="cards">
        <ns-multistore-dashboard inline-template>
            <div>
                <div class="flex -mx-4 mb-4">
                    <div class="px-4 w-1/3">
                        <div class="shadow rounded-lg p-3 bg-gradient-to-br from-blue-400 to-blue-600">
                            <h3 class="text-white font-semibold">{{ __( 'Complete Sales' ) }}</h3>
                            <h2 class="text-white font-bold text-4xl">@{{ getReportData( 'total_paid_orders' ) | currency }}</h2>
                            <div class="w-full flex justify-end">
                                <span class="text-sm text-gray-200">{{ __( 'Today' ) }} : +@{{ getReportData( 'day_paid_orders' ) | currency }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 w-1/3">
                        <div class="shadow rounded-lg p-3 bg-gradient-to-br from-green-400 to-green-600">
                            <h3 class="text-white font-semibold">{{ __( 'Income' ) }}</h3>
                            <h2 class="text-white font-bold text-4xl">@{{ getReportData( 'total_income' ) | currency }}</h2>
                            <div class="w-full flex justify-end">
                                <span class="text-sm text-gray-200">{{ __( 'Today' ) }} : +@{{ getReportData( 'day_income' ) | currency }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 w-1/3">
                        <div class="shadow rounded-lg p-3 bg-gradient-to-br from-red-400 to-red-600">
                            <h3 class="text-white font-semibold">{{ __( 'Waste' ) }}</h3>
                            <h2 class="text-white font-bold text-4xl">@{{ getReportData( 'total_wasted_goods' ) | currency }}</h2>
                            <div class="w-full flex justify-end">
                                <span class="text-sm text-gray-200">{{ __( 'Today' ) }} : +@{{ getReportData( 'day_wasted_goods' ) | currency }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="-mx-4 mb-4 flex flex-wrap">
                    <div class="px-4 w-1/2">
                        <div class="shadow rounded bg-white">
                            <div class="p-2 border-b border-gray-200">
                                <h3 class="font-semibold text-gray-700">{{ __( 'Stores Income' ) }}</h3>
                            </div>
                            <div v-if="! hasLoaded" class="h-56 flex items-center justify-center">
                                <ns-spinner></ns-spinner>
                            </div>
                            <div v-if="storeReport.length === 0 && hasLoaded" class=" h-56 flex items-center justify-center flex-col">
                                <span class="rounded-full h-28 w-28 bg-gray-600 text-gray-100 mb-4 flex items-center justify-center">
                                    <i class="las la-store text-6xl"></i>
                                </span>
                                <h3 class="font-semibold text-2xl text-gray-600">{{ __( 'Looks like there is no store' ) }}</h3>
                            </div>
                            <div
                                v-for="store of storeReport"
                                :key="store.id" 
                                class="border-b border-gray-200 p-2 flex justify-between">
                                <div class="flex-auto">
                                    <div class="flex -mx-2 justify-between">
                                        <div class="px-2">
                                            <h3 class="text-lg font-semibold text-gray-600">@{{ store.name }}</h3>
                                        </div>
                                        <div class="px-2">
                                            <h3 class="text-lg font-semibold text-gray-600">@{{ store.today_report ? store.today_report.day_income : 0 | currency }}</h3>
                                        </div>
                                    </div>
                                    <div class="flex flex-col -mx-2">
                                        <div class="px-2 py-1">
                                            <h4 class="text-semibold text-xs text-gray-500">
                                                <span>{{ __( 'Complete Sales :' ) }}</span>
                                                <span>@{{ store.today_report ? store.today_report.total_paid_orders : 0 | currency }}</span>
                                            </h4>
                                        </div>
                                        <div class="px-2 py-1">
                                            <h4 class="text-semibold text-xs text-gray-500">
                                                <span>{{ __( 'Total Expenses :' ) }}</span>
                                                <span>@{{ store.today_report ? store.today_report.total_expenses : 0 | currency }}</span>
                                            </h4>
                                        </div>
                                        <div class="px-2 py-1">
                                            <h4 class="text-semibold text-xs text-gray-500">
                                                <span>{{ __( 'Total Taxes :' ) }}</span>
                                                <span>@{{ store.today_report ? store.today_report.total_taxes : 0 | currency }}</span>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 w-1/2">
                        <div class="shadow rounded bg-white">
                            <div class="p-2 border-b border-gray-200">
                                <h3 class="font-semibold text-gray-700">{{ __( 'Other Details' ) }}</h3>
                            </div>
                            <div
                                class="border-b border-gray-200 flex justify-between">
                                <div class="flex-auto">
                                    <div class="flex border-b p-2 border-gray-200 -mx-2 justify-between">
                                        <div class="px-2">
                                            <h3 class="text-lg font-semibold text-gray-600">{{ __( 'Total Expenses' ) }}</h3>
                                            <h4 class="text-sm text-gray-500">{{ __( 'Yesterday' ) }}</h4>
                                        </div>
                                        <div class="px-2 flex flex-col items-end">
                                            <h3 class="text-lg font-semibold text-gray-600">@{{ getReportData( 'total_expenses' ) | currency }}</h3>
                                            <h4 class="text-sm text-gray-500">@{{ getReportData( 'total_expenses', 'yesterday_report' ) | currency }}</h4>
                                        </div>
                                    </div>
                                    <div class="flex border-b p-2 border-gray-200 -mx-2 justify-between">
                                        <div class="px-2">
                                            <h3 class="text-lg font-semibold text-gray-600">{{ __( 'Total Discounts' ) }}</h3>
                                            <h4 class="text-sm text-gray-500">{{ __( 'Yesterday' ) }}</h4>
                                        </div>
                                        <div class="px-2 flex flex-col items-end">
                                            <h3 class="text-lg font-semibold text-gray-600">@{{ getReportData( 'total_discounts' ) | currency }}</h3>
                                            <h4 class="text-sm text-gray-500">@{{ getReportData( 'total_discounts', 'yesterday_report' ) | currency }}</h4>
                                        </div>
                                    </div>
                                    <div class="flex border-b p-2 border-gray-200 -mx-2 justify-between">
                                        <div class="px-2">
                                            <h3 class="text-lg font-semibold text-gray-600">{{ __( 'Partially Paid Orders' ) }} (x@{{ getReportData( 'total_partially_paid_orders_count' ) }})</h3>
                                            <h4 class="text-sm text-gray-500">{{ __( 'Yesterday' ) }} (x@{{ getReportData( 'total_partially_paid_orders_count', 'yesterday_report' ) }})</h4>
                                        </div>
                                        <div class="px-2 flex flex-col items-end">
                                            <h3 class="text-lg font-semibold text-gray-600">@{{ getReportData( 'total_partially_paid_orders' ) | currency }}</h3>
                                            <h4 class="text-sm text-gray-500">@{{ getReportData( 'total_partially_paid_orders', 'yesterday_report' ) | currency }}</h4>
                                        </div>
                                    </div>
                                    <div class="flex border-b p-2 border-gray-200 -mx-2 justify-between">
                                        <div class="px-2">
                                            <h3 class="text-lg font-semibold text-gray-600">{{ __( 'Total Taxes' ) }}</h3>
                                            <h4 class="text-sm text-gray-500">{{ __( 'Yesterday' ) }}</h4>
                                        </div>
                                        <div class="px-2 flex flex-col items-end">
                                            <h3 class="text-lg font-semibold text-gray-600">@{{ getReportData( 'total_taxes' ) | currency }}</h3>
                                            <h4 class="text-sm text-gray-500">@{{ getReportData( 'total_taxes', 'yesterday_report' ) | currency }}</h4>
                                        </div>
                                    </div>
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
            data() {
                return {
                    storeReport : [],
                    hasLoaded: false,
                }
            },
            mounted() {
                this.loadStores();
            },
            computed: {
                // ...
            },
            methods: {
                getReportData( value, type = 'today_report' ) {
                    if ( this.storeReport.length > 0 ) {
                        const total =     this.storeReport.map( store => {
                            return store[ type ] ? store[ type ][ value ] : 0;
                        });

                        if ( total.length > 0 ) {
                            return total.reduce( ( b, a ) => b + a );
                        }
                    }
                    
                    return 0;
                },
                loadStores() {
                    this.hasLoaded  =   false;
                    nsHttpClient.get( '/api/nexopos/v4/multistores/stores-details' )
                        .subscribe( result => {
                            this.hasLoaded      =   true;
                            this.storeReport    =   result;
                        }, ( error ) => {
                            this.hasLoaded      =   true;
                            nsSnackBar.error( error.message ).subscribe();
                        });
                }
            }
        });
    </script>
@endsection