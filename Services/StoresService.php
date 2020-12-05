<?php
namespace Modules\NsMultiStore\Services;

use App\Classes\Hook;
use Illuminate\Support\Facades\Storage;
use Modules\NsMultiStore\Models\Store;

class StoresService 
{
    private $isMultiStore   =   false;
    private $currentStore;

    public function isMultiStore()
    {
        return $this->currentStore instanceof Store || $this->isMultiStore;
    }

    public function setStore( Store $store )
    {
        $this->currentStore     =   $store;
    }

    public function getCurrentStore()
    {
        return $this->currentStore;
    }

    public function current()
    {
        return $this->currentStore;
    }

    public function createStoreTables( Store $store )
    {
        $files  =   Storage::disk( 'ns' )->files( '/database/migrations/v1_0' );

        foreach( $files as $file ) {
            $className  =   collect( explode( '_', $file ) )->skipUntil( function( $value, $index ) {
                return $index === 5;
            })->map( function( $part ) {
                $newPart    =   explode( '.', $part );
                return ucwords( $newPart[0] );
            })->join('');

            include_once( base_path( $file ) );

            if ( class_exists( $className ) ) {
                /**
                 * provide the default prefix
                 * that will apply to all table created
                 */
                Hook::addFilter( 'ns-table-prefix', fn( $prefix ) => 'store_' . $store->id . '_' . $prefix );
                
                /**
                 * run migration
                 */
                $classObject    =   new $className;
                $classObject->up();

                Hook::removeAllFilters( 'ns-table-prefix' );
            }
        }
    }

    public function dismantleStore( Store $store )
    {
        $files  =   Storage::disk( 'ns' )->files( '/database/migrations/v1_0' );
        
        foreach( $files as $file ) {
            $className  =   collect( explode( '_', $file ) )->skipUntil( function( $value, $index ) {
                return $index === 5;
            })->map( function( $part ) {
                $newPart    =   explode( '.', $part );
                return ucwords( $newPart[0] );
            })->join('');

            include_once( base_path( $file ) );

            if ( class_exists( $className ) ) {
                /**
                 * provide the default prefix
                 * that will apply to all table created
                 */
                Hook::addFilter( 'ns-table-prefix', fn( $prefix ) => 'store_' . $store->id . '_' . $prefix );
                
                /**
                 * run migration
                 */
                $classObject    =   new $className;
                $classObject->down();

                Hook::removeAllFilters( 'ns-table-prefix' );
            }
        }

        $store->delete();
    }

    public function getOpened()
    {
        return Store::where( 'status', Store::STATUS_OPENED )->get();
    }

    public function defineStoreRoutes( $callback )
    {
        $this->isMultiStore     =   true;
        $callback();
        $this->isMultiStore     =   false;
    }
}