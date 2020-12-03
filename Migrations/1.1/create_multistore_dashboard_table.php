<?php
/**
 * Table Migration
 * @package  4.0
**/

namespace Modules\NsMultiStore\Migrations;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMultistoreDashboardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        Schema::create( 'nexopos_multistore_dashboard', function( Blueprint $table ) {
            $table->id();
            $table->float( 'total_unpaid_orders' )->default(0);
            $table->float( 'day_unpaid_orders' )->default(0);
            $table->float( 'total_unpaid_orders_count' )->default(0);
            $table->float( 'day_unpaid_orders_count' )->default(0);
            $table->float( 'total_paid_orders' )->default(0);
            $table->float( 'day_paid_orders' )->default(0);
            $table->float( 'total_paid_orders_count' )->default(0);
            $table->float( 'day_paid_orders_count' )->default(0);
            $table->float( 'total_partially_paid_orders' )->default(0);
            $table->float( 'day_partially_paid_orders' )->default(0);
            $table->float( 'total_partially_paid_orders_count' )->default(0);
            $table->float( 'day_partially_paid_orders_count' )->default(0);
            $table->float( 'total_income' )->default(0);
            $table->float( 'day_income' )->default(0);
            $table->float( 'total_discounts' )->default(0);
            $table->float( 'day_discounts' )->default(0);
            $table->float( 'day_taxes' )->default(0);
            $table->float( 'total_taxes' )->default(0);
            $table->float( 'total_wasted_goods_count' )->default(0);
            $table->float( 'day_wasted_goods_count' )->default(0);
            $table->float( 'total_wasted_goods' )->default(0);
            $table->float( 'day_wasted_goods' )->default(0);
            $table->float( 'total_expenses' )->default(0);
            $table->float( 'day_expenses' )->default(0);
            $table->integer( 'total_stores' )->default(0);
            $table->integer( 'day_stores' )->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        // drop tables here
    }
}
