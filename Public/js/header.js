const nsMultiStoreStorePopup    =   Vue.component( 'ns-multistore-popup', {
    template: '#store-selection-template',
    data() {
        return {
            stores: [],
            hasLoaded: false
        }
    },
    mounted() {
        this.loadStores();
        this.$popup.event.subscribe( action => {
            if ( action.event === 'click-overlay' ) {
                this.$popup.close();
            }
        })
    },
    methods: {
        close() {
            this.$popup.close();
        },
        loadStores() {
            this.hasLoaded      =   false;
            nsHttpClient.get( '/api/nexopos/v4/multistores/stores' )
                .subscribe( stores => {
                    this.stores     =   stores;
                    this.hasLoaded  =   true;
                }, ( error ) => {
                    this.hasLoaded  =   true;
                })
        }
    }
});

Vue.component( 'ns-multistore-selector', {
    mounted() {

    },
    methods: {
        toggleStoreSelector() {
            Popup.show( nsMultiStoreStorePopup )
        }
    }
})