<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOriginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('origins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('host');
            $table->integer('application_id')->unsigned();
            $table->unique(['host', 'application_id']);
        });

        Schema::table('origins', function(Blueprint $table){
           $table->foreign('application_id')
               ->references('id')->on('applications')
               ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('origins', function(Blueprint $table){
           $table->dropForeign('origins_application_id_foreign');
        });
        Schema::drop('origins');
    }
}
