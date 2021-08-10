<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id("idAccount");
            $table->string("username")->unique();
            $table->string("password",1024);
            $table->boolean("enabledPurchase")->default(true);
            $table->boolean("enabledLogin")->default(true);
            $table->boolean("enabledNotification");
            $table->boolean("enabledEmails");
            $table->string("vkey",1024);
            $table->foreignId('idRole')->references('idRole')->on('roles')->constrained()->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreignId('idType')->references('idTypeAcc')->on('type_accounts')->constrained()->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreignId('idUser')->references('idUser')->on('users')->constrained()->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->string('api_token', 80)->unique()->nullable()->default(null);
            $table->integer("loginAttemps")->default(0);;
            $table->boolean("isDeleted")->default(false);
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
        Schema::dropIfExists('accounts');
    }
}
