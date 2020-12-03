<div id="dashboard-header" class="w-full flex justify-between p-4">
    <div class="flex items-center">
        <div>
            <div @click="toggleSideMenu()" class="hover:bg-white hover:text-gray-700 hover:shadow-lg hover:border-opacity-0 border border-gray-400 rounded-full h-10 w-10 cursor-pointer font-bold text-2xl justify-center items-center flex text-gray-800">
                <i class="las la-bars"></i>
            </div>
        </div>
        <div class="ml-3">
            @if( ns()->store->isMultiStore() )
            <h2 class="font-bold text-2xl text-gray-700">{{ ns()->store->getCurrentStore()->name }}</h2>
            @else
            <h2 class="font-bold text-2xl text-gray-700">{{ __( 'Warehouse' ) }}</h2>
            @endif
        </div>
    </div>
    <div class="top-tools-side flex items-center -mx-2">
        <div clss="px-2">
            <ns-notifications></ns-notifications>
        </div>
        <div class="px-2">
        <a href="{{ route( 'ns.multistore-stores' ) }}" class="hover:bg-white hover:text-gray-700 hover:shadow-lg hover:border-opacity-0 rounded-full h-12 w-12 cursor-pointer font-bold text-2xl justify-center items-center flex text-gray-800 border border-gray-400">
            <i class="las la-store"></i>
        </a>
        </div>
        <div class="px-2">
            <ns-multistore-selector inline-template class="border-gray-400 rounded-lg flex border py-2 justify-center hover:border-opacity-0 cursor-pointer hover:shadow-lg hover:bg-white">
                <div @click="toggleStoreSelector()" class="flex justify-between items-center flex-shrink-0">
                    <span class="hidden md:inline-block text-gray-600 px-2">{{ __( 'Stores' ) }}</span>
                    <div class="px-2">
                        <div class="w-8 h-8 rounded-full bg-blue-400 text-white flex items-center justify-center">
                            {{ ns()->option->get( 'ns.multistores.stores', 0 ) }}
                        </div>
                    </div>
                </div>
            </ns-multistore-selector>
        </div>
        <div class="px-2">
            <div @click="toggleMenu()" :class="menuToggled ? 'bg-white border-transparent shadow-lg rounded-t-lg' : 'border-gray-400 rounded-lg'" class="w-32 md:w-56 flex flex-col border py-2 justify-center hover:border-opacity-0 cursor-pointer hover:shadow-lg hover:bg-white">
                <div class="flex justify-between items-center flex-shrink-0">
                    <span class="hidden md:inline-block text-gray-600 px-2">{{ sprintf( __( 'Howdy, %s' ), Auth::user()->username ) }}</span>
                    <span class="md:hidden text-gray-600 px-2">{{ sprintf( __( '%s' ), Auth::user()->username ) }}</span>
                    <div class="px-2">
                        <div class="w-8 h-8 rounded-full bg-gray-800"></div>
                    </div>
                </div>
            </div>
            <div v-cloak class="w-32 md:w-56 shadow-lg flex z-10 absolute -mb-2 rounded-br-lg rounded-bl-lg overflow-hidden" v-if="menuToggled">
                <ul class="text-gray-700 w-full bg-white">
                    @if ( Auth::user()->allowedTo([ 'manage.profile' ]) )
                    <li class="hover:bg-blue-400 bg-white hover:text-white"><a class="block px-2 py-1" href="{{ ns()->route( 'ns.dashboard.users.profile' ) }}"><i class="las text-lg mr-2 la-user-tie"></i> {{ __( 'Profile' ) }}</a></li>
                    @endif
                    <li class="hover:bg-blue-400 bg-white hover:text-white"><a class="block px-2 py-1" href="{{ ns()->route( 'ns.logout' ) }}"><i class="las la-sign-out-alt mr-2"></i> {{ __( 'Logout' ) }}</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

@section( 'layout.dashboard.footer.inject' )
    @parent
    <script type="text/x-template" id="store-selection-template">
        <div class="bg-white shadow-xl w-95vw md:w-2/3-screen lg:w-1/3-screen h-95vh md:h-2/3-screen lg:h-2/3-screen overflow-hidden flex flex-col">
            <div class="header border-b p-2 flex justify-between items-center">
                <h3 class="font-semibold text-gray-700">Stores</h3>
                <div>
                    <ns-close-button @click="close()"></ns-close-button>
                </div>
            </div>
            <div class="overflow-y-auto flex-auto">
                <div v-if="hasLoaded === 0" class="h-full w-full flex items-center justify-center">
                    <ns-spinner></ns-spinner>
                </div>
                <div v-if="hasLoaded && stores.length === 0" class="h-full w-full flex items-center justify-center">
                    <h3>{{ __( 'No store has been created.' ) }}</h3>
                </div>
                <div v-if="hasLoaded && stores.length > 0" class="grid grid-cols-3 w-full">
                    <a :href="'{{ url( '/dashboard/store' ) }}/' + store.id" v-for="store of stores" class="border bg-blue-800 cursor-pointer border-gray-200 h-40 relative">
                        <div class="h-full w-full object-contain overflow-hidden flex items-center justify-center">
                            <img v-if="store.thumb" :src="store.thumb" :alt="store.name">
                            <img v-if="! store.thumb" class="w-24" :src="'/modules/nsmultistore/assets/images/shop.png'" :alt="store.name">
                        </div>
                        <div class="h-16 bottom-0 absolute w-full z-10 p-2 flex items-center flex-col justify-center text-white font-semibold" style="background: rgb(0,0,0);
background: linear-gradient(0deg, rgba(0,0,0,0.8379726890756303) 0%, rgba(0,0,0,0.7147233893557423) 32%, rgba(0,212,255,0) 100%);">
                            <span>@{{ store.name }}</span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="flex w-full">
                <a href="{{ route( 'ns.multistore-stores' ) }}" class="cursor-pointer flex-auto text-2xl h-16 p-2 flex items-center justify-center bg-green-400 hover:bg-green-500 text-white">
                    <i class="las la-store mr-2"></i>
                    <span>{{ __( 'Stores' ) }}</span>
                </a>
                <div class="cursor-pointer flex-auto text-2xl h-16 p-2 flex items-center justify-center bg-gray-200 hover:bg-gray-300 text-gray-700" @click="close()" >
                    <i class="las la-times mr-2"></i>
                    <span>{{ __( 'Cancel' ) }}</span>
                </div>
            </div>
        </div>
    </script>
    <script src="{{ asset( 'modules/nsmultistore/js/header.js' ) }}"></script>
@endsection