<?php
/**
 * Table Migration
 * @package  4.0
**/

namespace Modules\NsMultiStore\Migrations;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        if ( ! Schema::hasTable( 'nexopos_stores' ) ) {
            Schema::create( 'nexopos_stores', function( Blueprint $table ) {
                $table->id();
                $table->string( 'name' );
                $table->string( 'slug' )->unique();
                $table->integer( 'author' );
                $table->text( 'description' )->nullable();
                $table->string( 'thumb' )->nullable();
                $table->string( 'status' )->default(1);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::dropIfExists( 'nexopos_stores' );
    }
}
