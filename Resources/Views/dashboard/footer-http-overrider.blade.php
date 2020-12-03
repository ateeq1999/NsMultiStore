@if( ns()->store->isMultiStore() )
<script>
ns.storeID  =   '{{ ns()->store->getCurrentStore()->id }}';
</script>
<script src="{{ asset( '/modules/nsmultistore/js/http-overrider.js' ) }}"></script>
@endif