<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id("idOrder");
            $table->foreignId('idAccount')->references('idAccount')->on('accounts')->constrained()->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->dateTime('time')->useCurrent();
            $table->foreignId('idStatus')->references('idStatus')->on('status')->constrained()->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->dateTime("startTime");
            $table->dateTime("endTime");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
