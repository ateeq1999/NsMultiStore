
nsHooks.addFilter( 'http-client-url', 'ns-multistore', ( url ) => {

    const validURL  =   (str) => {
        var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
            '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
            '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
            '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
            '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
            '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
        return !!pattern.test(str);
    }

    /**
     * if the provided string is a URL
     * we believe it has been used with ns()->url( ... )
     * therefore, we don't need to adjust the URL.
     */
    if (  ! validURL( url ) ) {
        const parts         =   url.split( '/' );
        const index         =   parts.indexOf( 'v4' );
        const multistore    =   parts.indexOf( 'stores' );
    
        /**
         * if the url already included the "stores"
         * segment we assume the url has previously been edited
         * therefore, no need to update the URL.
         */
        if ( multistore >= 0 ) {
            return url;
        }
        
        parts.splice( index + 1, 0, `store/${ns.storeID}` );
    
        return parts.join( '/' );
    }

    return url;

});