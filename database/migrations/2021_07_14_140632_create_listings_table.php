<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id("idListing");
            $table->string("title");
            $table->longText("description");
            $table->foreignId('idLocation')->references('idLocation')->on('locations')->constrained()->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreignId('idType')->references('idListingType')->on('listing_types')->constrained()->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->boolean("availibility");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listings');
    }
}
