<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExternalAuthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('external_auth', function (Blueprint $table) {
            $table->id();
            $table->foreign('id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('provider');
            $table->string('provider_id');
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
        Schema::dropIfExists('external_auth');
    }
}
